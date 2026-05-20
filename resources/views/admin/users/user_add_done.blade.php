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
<!--利用者ID：<php echo $request->id ?><br>-->
氏名：<?php echo $request->name ?><br>
メールアドレス：<?php echo $request->email ?><br>
<br>
@endsection