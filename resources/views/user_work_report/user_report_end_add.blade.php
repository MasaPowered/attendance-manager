@extends('layouts.user_report_menu')

@section('title', '退勤時業務報告')

@section('content')

<?php if ($login_lock_flg) : ?>
    ログイン時間外です。
<?php else : ?>
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
    <form method="POST">
        @csrf
        【業務内容】<br>
        <textarea name="report" class="commentTextArea"></textarea><br>
        <br>
        <input name="submit" type="submit" value="送信">
    </form>
    <br>
    <?php foreach ($message_array as $value) : ?>
        <?php echo $value->date ?> <?php if (!empty($value->leavetime)) echo date('H:i', strtotime($value->leavetime)) ?> <?php echo $value->user_id ?> <?php echo $value->name ?><br>
        <?php echo $value->report ?>
        <br><br>
    <?php endforeach; ?>
<?php endif; ?>
@endsection