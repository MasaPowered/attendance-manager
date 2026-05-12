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

//サニタイズ
if (!empty($_POST)) {
    $post = sanitize($_POST);
} else {
    $error_message[] = "内容を取得できませんでした。";
}

$login_message = session_check_user();

$userid = $_SESSION['user_id'];

if (empty($post["oldpass"])) {
    $error_message[] = "古いパスワードが入力されていません。";
}

if (empty($post["pass"])) {
    $error_message[] = "パスワードが入力されていません。";
}

if (strcmp($post["pass"], $post["pass2"]) !== 0) {
    $error_message[] = "パスワードが一致しません。";
}

//暗号化
$post["oldpass"] = md5($post["oldpass"]);
$post["pass"] = md5($post["pass"]);

if (empty($error_message)) {
    //DB接続
    include("../setting/db_setting.php");
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $sql = $dbh->prepare('SELECT * FROM user_table WHERE user_id FROM user_table WHERE user_id = :userid AND pass = :pass');
        $sql->bindParam(': userid', $userid, PDO::PARAM_STR);
        $sql->bindParam(': pass', $post["oldpass"], PDO::PARAM_STR);

        //SQL クエリの実行
        $res = $sql->execute();
        $rec = $sql->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // $error_message[] = "現在のパスワードが違います。";
    }

    if ($rec == false) {
        $error_message[] = "現在のパスワードが違います。 ";
    } else {
        $res = null;
        $rec = null;
        $sql = null;
        // トランザクション開始
        $dbh->beginTransaction();

        try {
            //SQL作成
            $sql = $dbh->prepare("UPDATE user_table SET pass = :pass WHERE user_id = :userid");
            //値をセット
            $sql->bindParam(':userid', $userid, PDO::PARAM_STR);
            $sql->bindParam(':pass', $post["pass"], PDO::PARAM_STR);
            //SQL実行
            $res = $sql->execute();
            // コミット
            $res = $dbh->commit();
        } catch (Exception $e) {
            // ロールバック
            $dbh->rollBack();
            $error_message[] = "変更に失敗しました。";
        }

        if ($res) {
            $success_message = "変更しました。";
            //2023/10/31 ログ
            ////////////////////////////////////////////////////////////////////////////////////////////////
            $info = new LogWrite();

            $info->append('user(' . $_SESSION['user_id'] . '): user_pass_edit[' . $userid . ']')
                ->newline()
                ->commit(LogWrite::APPEND);
            ////////////////////////////////////////////////////////////////////////////////////////////////
        }
    }
    $dbh = null;
}

/*if (!empty($error_message)) {
    header('Location: user_pass_edit_done.php');
}*/
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
    include("../layouts/user_report_menu.php");
    ?>
    <h1 class="title">パスワード変更</h1>
    <hr>
    <!-- 登録成功時メッセージ -->
    <?php if (!empty($success_message)) : ?>
        <p class="success_message"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <!-- エラーメッセージ -->
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
</body>

</html>