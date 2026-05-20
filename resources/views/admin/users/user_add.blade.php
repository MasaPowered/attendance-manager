@extends('layouts.report_menu')

@section('title', '利用者追加')

@section('content')

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

<form method="POST" action="user_add_check">
    @csrf
    <!--利用者ID：<br>
    <input id="id" type="text" name="id" maxlength="20"><br>-->
    氏名：<br>
    <input id="name" type="text" name="name"><br>
    メールアドレス：<br>
    <input id="email" type="text" name="email" maxlength="20"><br>
    パスワード：<br>
    <input id="pass" type="password" name="pass"><br>
    パスワードをもう一度入力してください：<br>
    <input id="pass2" type="password" name="pass2"><br>
    <br>
    <input id="button" type="submit" name="submitbtn" value="OK">
</form>
@endsection