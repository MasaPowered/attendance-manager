@extends('layouts.report_menu')

@section('title', '管理者追加')

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
氏名：<?php echo $data["name"] ?><br>
メールアドレス：<?php echo $data["email"] ?><br>
<br>
@endsection