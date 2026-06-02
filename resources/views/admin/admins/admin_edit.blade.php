@extends('layouts.report_menu')

@section('title', '管理者編集')

@section('content')
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST" action="{{ route('admin.admins.edit_done') }}">
        @csrf
        管理者ID:
        <input type="hidden" name="id" value="<?php echo $message_array->id ?>">
        @error('id')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        <?php echo $message_array->id ?><br>
        氏名:<br>
        <input type="text" name="name" value="<?php echo $message_array->name ?>"><br>
        @error('name')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        メールアドレス：<br>
        <input id="email" type="text" name="email" maxlength="20" value="<?php echo $message_array->email ?>"><br>
        @error('email')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        パスワード: <br>
        <input type="password" name="pass"><br>
        @error('pass')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        パスワードをもう一度入力してください: <br>
        <input id="pass2" type="password" name="pass2"><br>
        @error('pass2')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        <input type="submit" value="保存" name="submitbtn">
    </form>
<?php endif; ?>
@endsection