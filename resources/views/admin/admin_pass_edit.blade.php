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

$login_message = session_check_admin();

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
    <h1 class="title">パスワード変更</h1>
    <hr>
    <!-- エラーメッセージ -->
    <?php if (!empty($error_message)) : ?>
        <?php foreach ($error_message as $value) : ?>
            <div class="error_message">※<?php echo $value; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="admin_pass_edit_done.php">
        <input type="hidden" name="id" value="<?php echo $userid ?>">
        古いパスワード：<br>
        <input id="oldpass" type="password" name="oldpass" maxlength="32"><br>
        新しいパスワード：<br>
        <input id="pass" type="password" name="pass" maxlength="32"><br>
        新しいパスワードをもう一度入力してください：<br>
        <input id="pass2" type="password" name="pass2" maxlength="32"><br>
        <input type="submit" value="保存" name="submitbtn">
    </form>
</body>

</html>