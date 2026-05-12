@extends('layouts.report_menu')

@section('title', '利用者一覧')

@section('content')

<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST" action="user_edit_done">
        @csrf
        @foreach($message_array as $value)
        利用者ID:
        <input type="hidden" name="id" value=<?php echo $value->id ?>>
        <?php echo $value->id ?><br>
        氏名:<br>
        <input type="text" name="name" value=<?php echo $value->name ?>><br>
        @break
        @endforeach
        パスワード: <br>
        <input type="password" name="pass"><br>
        パスワードをもう一度入力してください: <br>
        <input id="pass2" type="password" name="pass2"><br>
        <input type="submit" value="保存" name="submitbtn">
    </form>
<?php endif; ?>
@endsection