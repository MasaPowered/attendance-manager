@extends('layouts.report_menu')

@section('title', 'シフト一括削除')

@section('content')
<!-- 成功メッセージ -->
<?php if (!empty($success_message)) : ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>

<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>
@endsection