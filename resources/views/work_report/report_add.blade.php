<?php

require_once('../common/common.php');

//初期化
$message_array = array();
$success_message = null;
$error_message = array();
$dbo = null;
$sql = null;
$res = null;
$rec = null;
//$updateflg = true;
$radioStartChk = false;
$radioEndChk = false;

$login_message = session_check_admin();

//サニタイズ
if (isset($_POST)) {
    $post = sanitize($_POST);
} else {
    $error_message[] = "内容を取得できませんでした。";
}
//データベース接続
try {
    include("../setting/db_setting.php");
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //$error_message[] = $e->getMessage();
    $error_message[] = "データベースに接続できません。";
}

//出勤退勤チェックが押されたら
if (!empty($post)) {
    if (empty($post["user_id"])) {
        $error_message[] = "利用者IDが入力されていません。";
    } else {
        //利用者登録チェック
        try {
            //SQL作成
            $sql = $dbh->prepare("SELECT user_id, name FROM user_table WHERE user_id=?");
            $data[] = $post['user_id'];
            //SQLクエリの実行
            $res = $sql->execute($data);
            $rec = $sql->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }
        if (empty($rec)) {
            $error_message[] = "このユーザーは登録されていません。";
        }
        $res = null;
        $rec = null;
    }

    //遅刻時間が入力されてない場合NULL
    if (empty($post["latetime"])) {
        $post["latetime"] = NULL;
    }

    //日付が入力されてない場合
    if (empty($post["date"])) {
        //現在の日付を入力
        $post["date"] = date("y-m-d");
    }

    //レポート重複チェック
    if (empty($post["radio"])) {
        $error_message[] = "出退勤のラジオボタンを押してください。";
    } else if ($post["radio"] == "start") {
        $radioStartChk = true;
        $radioEndChk = false;
        try { //SQL作成 
            $sql = $dbh->prepare("SELECT userid FROM start_report_table WHERE date=:date AND userid=:user_id");
            $sql->bindParam(':date', $post["date"], PDO::PARAM_STR);
            $sql->bindParam(':user_id', $post["user_id"], PDO::PARAM_STR);

            //SQLクエリの実行
            $res = $sql->execute();
            $rec = $sql->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error_message[] = $e->getMessage();
            // まだデータがないため追加する
            //$updateflg = false;
        }

        if (!empty($rec)) {
            $error_message[] = "データがすでに存在します。";
        }

        $res = null;
        $rec = null;
    } else if ($post["radio"] == "end") {
        $radioStartChk = false;
        $radioEndChk = true;
        try {
            //SQL作成
            $sql = $dbh->prepare("SELECT * FROM end_report_table WHERE date=:date AND userid=:user_id");
            $sql->bindParam(':date', $post["date"], PDO::PARAM_STR);
            $sql->bindParam(':user_id', $post["user_id"], PDO::PARAM_STR);

            //SQLクエリの実行
            $res = $sql->execute();
            $rec = $sql->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            //$error_message[] = $e->getMessage();
            //まだデータがないため追加する
            //$updateflg = false;
        }
        if (!empty($rec)) {
            $error_message[] = "データがすでに存在します。";
        }
        $res = null;
        $rec = null;
    }
    /*if ($rec['date'] == $post['date']) {
        $error_message[] = "すでにこの日付の報告書は存在します。";
    }*/

    //遅刻時間がある場合
    /*if (empty($post["latetime"])) {
        if ($post["latetime"])
        //現在の日付を入力
        $post["latetime"] = date("Y-m-d H:i:s");
    }*/

    //出勤にチェックがあるとき出勤報告内容をセット
    /*if (!empty($post["submitstart"])) {
        $work_report_start = $post['startreport'];
    }

    //退勤にチェックがあるとき退勤報告内容をセット
    if(!empty($post["submitstart"])) {
        $work_report_end = $post['endreport'];
    }
    if (!isset($post['start']) && !isset($post['end'])) {
        $error_message[] = "出勤退勤どちらかにチェックをいれてください。";
    }*/

    if (empty($error_message)) {
        //更新
        /*if ($updateflg) {
        //出勤ボタン
        if (!empty($post["submitstart"])) {
            // トランザクション開始
            $dbh->beginTransaction();
            try {
                //SQL作成
                $statment = $dbh->prepare("UPDATE workreport_table SET
                start_check=:sch,
                latetime=:latetime,
                work_report_start=:wrs,
                WHERE report_id=:reportid");
                // 値をセット
                $statment->bindParam(':sch', true, PDO::PARAM_STR);
                $statment->bindParam(':latetime', $post["latetime"], PDO::PARAM_STR);
                $statment->bindParam(':wrs', $work_report_start, PDO::PARAM_STR);
                $statment->bindParam(':reportid', $rec[ 'report_id'], PDO::PARAM_STR);
                //SQL実行
                $res = $statment->execute();

                // コミット
                $res = $dbh->commit();
            } catch (Exception $e) {
                //ロールバック
                $dbh->rollBack();
            }
            if ($res) {
                $success_message = "修正しました。";
            } else {
                $error_message[] = "修正に失敗しました。";
            }
            $statment = null;
        }
        //退勤ボタン
        if (!empty($post["submitend"])) {
            // トランザクション開始
            $dbh->beginTransaction();
            try {
                //SQL作成
                $statment = $dbh->prepare("UPDATE workreport_table SET
                end_check=:ech,
                latetime=: latetime,
                work_report_end=:wre
                WHERE report_id=: reportid");

                //値をセット
                $statment->bindParam(':ech', true, PDO::PARAM_STR);
                $statment->bindParam(':latetime', $post["latetime"], PDO::PARAM_STR);
                $statment->bindParam(':wre', $work_report_end, PDO::PARAM_STR);
                $statment->bindParam(':reportid', $rec['report_id'], PDO::PARAM_STR);

                //SQL実行
                $res = $statment->execute();

                // コミット
                $res = $dbh->commit();
            } catch (Exception $e) {
                // ロールバック
                $dbh->rollBack();
            }

            if ($res) {
                $success_message = "修正しました。";
            } else {
                $error_message[] = "修正に失敗しました。";
            }
            $statment = null;
        } else {*/
        //出勤
        if ($post["radio"] == "start") {
            echo $post["latetime"];
            // トランザクション開始
            $dbh->beginTransaction();
            try {
                //SQL作成
                $statment = $dbh->prepare("INSERT INTO start_report_table(userid, date, arrivalcheck, latetime, report)
                VALUES (:user_id, :date, true, :latetime, :wrs)");

                $statment->bindParam(':user_id', $post["user_id"], PDO::PARAM_STR);
                $statment->bindParam(':date', $post["date"], PDO::PARAM_STR);
                $statment->bindParam(':latetime', $post["latetime"], PDO::PARAM_STR);
                $statment->bindParam(':wrs', $post["report"], PDO::PARAM_STR);

                //SQL実行
                $res = $statment->execute();
                // コミット
                $res = $dbh->commit();
            } catch (Exception $e) {
                //ロールバック
                $dbh->rollBack();
            }

            if ($res) {
                $success_message = "追加しました。";
            } else {
                $error_message[] = "追加に失敗しました。";
            }

            $res = null;
            $statment = null;
        } else if ($post["radio"] == "end") {
            // トランザクション開始
            $dbh->beginTransaction();
            try {
                //SQL作成
                $statment = $dbh->prepare("INSERT INTO end_report_table(userid, date, leavecheck, report)
                VALUES (:user_id, :date, true, :wre)");

                $statment->bindParam(':user_id', $post["user_id"], PDO::PARAM_STR);
                $statment->bindParam(':date', $post["date"], PDO::PARAM_STR);
                $statment->bindParam(':wre', $post["report"], PDO::PARAM_STR);
                //SQL実行
                $res = $statment->execute();
                // コミット
                $res = $dbh->commit();
            } catch (Exception $e) {
                //ロールバック
                $dbh->rollBack();
            }

            if ($res) {
                $success_message = "追加しました。";
            } else {
                $error_message[] = "追加に失敗しました。";
            }
            $res = null;
            $statment = null;
        }
    }
}
$dbh = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠システム</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php echo $login_message ?>
    <?php
    include("../layouts/report_menu.php");
    ?>
    <h1 class="title">業務報告追加</h1>
    <hr>
    <!-- 登録成功時メッセージ -->
    <?php if (!empty($success_message)) : ?>
        <p class="success_message"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <!-- エラーメッセージ -->
    <?php if (!empty($error_message)) : ?>
        <?php foreach ($error_message as $value) : ?>
            <div class="error_message">※<?php echo $value; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <form method="POST">
        【出勤報告】<input id="startcheck" type="radio" name="radio" value="start" <?php if ($radioStartChk) echo "checked" ?>>
        【退勤報告】<input id="endcheck" type="radio" name="radio" value="end" <?php if ($radioEndChk) echo "checked" ?>>
        <br>
        【利用者ID】<br>
        <input id="userid" type="text" name="user_id" maxlength="20"><br>
        【業務内容】<br>
        <textarea name="report" class="commentTextArea"></textarea><br>
        【遅刻の場合遅刻時間を入力してください。】 <br>
        <input id="latetime" type="time" name="latetime" disabled><br>
        【日付】<br>
        <input type="date" name="date" maxlength="30" value=<?php echo date("Y-m-d"); ?>><br>
        <br>
        <input id="button" type="submit" value="ＯＫ">
    </form>
    <br>
    <script>
        var startcheck = document.getElementById('startcheck');
        var endcheck = document.getElementById('endcheck');
        var userid = document.getElementById('userid');
        var latetime = document.getElementById('latetime');
        var button = document.getElementById('button');
        //出勤が選ばれた時だけ遅刻時間を入力できる
        startcheck.addEventListener('click', (e) => {
            latetime.disabled = false;
        })
        endcheck.addEventListener('click', (e) => {
            latetime.disabled = true;
        })

        //ユーザーIDが入力されたらOKボタンが押せる
        /*userid.addEventListener('keydown', (e) => {
            if (e.target.value.length >= 1) {
                button.disabled = false;
            } else {
                button.disabled = true;
            }
        })*/
    </script>
</body>

</html>