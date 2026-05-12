@extends('layouts.report_menu')

@section('title', '利用者一覧')

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

<table border="1">
    <tr>
        <td>利用者ID</td>
        <td>名前</td>
    </tr>
    <?php if (!empty($success_message)) : ?>
        <tr>
            <td><?php echo $id ?></td>
            <td><?php echo $name ?></td>
        </tr>
    <?php endif; ?>
</table>
@endsection