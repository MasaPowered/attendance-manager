@extends('layouts.report_menu')

@section('title', '管理者追加')

@section('content')

<?php echo $login_message = 'debug' ?>

<!-- 登録成功時メッセージ -->
<?php if (!empty($success_message)) : ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        |<div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="{{ route('admin.admins.add_check') }}">
    @csrf
    氏名：<br>
    <input id="name" type="text" name="name" maxlength="20"><br>
    メールアドレス：<br>
    <input id="email" type="text" name="email" maxlength="20"><br>
    パスワード：<br>
    <input id="pass" type="password" name="pass" maxlength="32"><br>
    パスワードをもう一度入力してください：<br>
    <input id="pass2" type="password" name="pass2" maxlength="32"><br>
    <br>
    <input id="button" type="submit" name="submitbtn" value="ＯＫ">
</form>
@endsection