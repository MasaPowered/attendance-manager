@extends('layouts.report_menu')

@section('title', '利用者ログイン制限設定')

@section('content')

<!-- 登録成功時メッセージ -->
<?php if (!empty($success_message)) : ?>
    <div class="success_message"><?php echo $success_message; ?></div>
<?php endif; ?>
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST">
        @csrf
        <p>ログイン制限をかける場合チェックをつけてください。</p>
        【ログイン制限】
        <input type="checkbox" name="logintime_status" <?php if (!empty($message_array->logintime_status)) echo 'checked'; ?>>
        【開始時間】
        <input id="start_time" type="time" name="start_time" value=<?php echo $message_array->start_time; ?>>
        ～
        【終了時間】
        <input id="end_time" type="time" name="end_time" value=<?php echo $message_array->end_time; ?>>
        <br>
        <input type="submit" value="保存" name="submit">
    </form>
<?php endif; ?>
@endsection