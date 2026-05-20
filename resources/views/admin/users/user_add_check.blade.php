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
<?php else : ?>
    こちらの内容を登録してもよろしいですか?: <br>
    <form method="POST" action="user_add_done">
        @csrf
        <!--<input type="hidden" name="id" value=<php echo $data["id"] ?>>-->
        <input type="hidden" name="name" value=<?php echo $data["name"] ?>>
        <input type="hidden" name="email" value=<?php echo $data["email"] ?>>
        <input type="hidden" name="pass" value=<?php echo $data["pass"] ?>>
        <!--利用者ID：<php echo $data["id"] ?><br>-->
        氏名：<?php echo $data["name"] ?><br>
        メール：<?php echo $data["email"] ?><br>
        <br>
        <input id="button" type="submit" name="submitbtn" value="ＯＫ">
    </form>
<?php endif; ?>
@endsection