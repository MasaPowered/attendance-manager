@extends('layouts.report_menu')

@section('title', '業務報告削除')

@section('content')

<!-- 成功メッセージ -->
<?php if (!empty($success_message)) : ?>
    <?php foreach ($success_message as $value) : ?>
        <div class="success_message"><?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

@endsection