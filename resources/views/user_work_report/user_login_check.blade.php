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
if (isset($_POST)) {
    $post = sanitize($_POST);
} else {
    $error_message[] = "内容を取得できませんでした。";
}

if (empty($post["user_id"])) {
    $error_message[] = "利用者IDが入力されていません。";
}

if (empty($post["pass"])) {
    $error_message[] = "パスワードが入力されていません。 ";
}

//暗号化
$post["pass"] = md5($post["pass"]);

//データベース接続
try {
    include("../setting/db_setting.php");
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //$error_message[] = $e->getMessage();
}

if (empty($error_message)) {
    try {
        //SQL作成
        $sql = $dbh->prepare("SELECT user_id, name FROM user_table WHERE user_id=? AND pass=?");
        $data[] = $post['user_id'];
        $data[] = $post['pass'];

        //SQLクエリの実行
        $res = $sql->execute($data);
        $rec = $sql->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }
    if ($rec == false) {
        $error_message[]  = "IDかパスワードが間違っています。 ";
    } else {
        $_SESSION = array();

        session_start();
        $_SESSION['login'] = 1;
        $_SESSION['user_id'] = $rec['user_id'];
        $_SESSION['user_name'] = $rec['name'];
        //2023/10/31 ログ
        ///////////////////////////////////////////////////////////////////////////////////
        $info = new LogWrite();
        $info->append('user(' . $rec['user_id'] . '): user_login')
            ->newline()
            ->commit(LogWrite::APPEND);
        ///////////////////////////////////////////////////////////////////////////////////
        header('Location:user_report_start_add.php');
        exit();
    }
    $rec = null;
}
$dbh = null;
?>

<?php if (!empty($error_message)) : ?>
    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>勤怠システム</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <header class="menu">
            <ul>
                |
                <li><a>トップ</a></li>
            </ul>
        </header>
        <h1 class="title">利用者ログイン画面</h1>
        <hr>

        <!-- エラーメッセージ -->
        <?php if (!empty($error_message)) : ?>
            <?php foreach ($error_message as $value) : ?>
                <div class="error_message">※<?php echo $value; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <br>
        <a href="user_login.php">戻る</a>
    </body>

    </html>
<?php endif; ?>