<?php

require_once('../common/common.php');

$login_message = session_check_admin();

//2023/10/30 ログ
/////////////////////////////////////////////////////////////////////////////
$info = new LogWrite();
$info->append('admin(' . $_SESSION['user_id'] . '): admin_logout')
    ->newline()
    ->commit(LogWrite::APPEND);
/////////////////////////////////////////////////////////////////////////////

//session_start();
$_SESSION = array();
if (isset($_COOKIE[session_name()]) == true) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();
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
    <header class="menu">
        <ul>
            <li><a>トップ</a></li>
        </ul>
    </header>
    <h1 class="title">ログアウト</h1>
    <hr>
    <!-- エラーメッセージ -->
    <?php if (!empty($error_message)) : ?>
        <?php foreach ($error_message as $value) : ?>
            <div class="error_message">※<?php echo $value; ?></div> <?php endforeach; ?>
    <?php endif; ?>
    <br>
    <a href="admin_login.php">ログイン</a>
</body>

</html>