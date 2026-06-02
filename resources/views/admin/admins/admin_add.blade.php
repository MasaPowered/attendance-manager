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
        |<div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="{{ route('admin.admins.add_check') }}">
    @csrf
    @if (session('error_general'))
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ session('error_general') }}
        </div>
    @endif
    氏名：<br>
    <input id="name" type="text" name="name" maxlength="20" value="{{old('name')}}"><br>
    @error('name')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    メールアドレス：<br>
    <input id="email" type="text" name="email" maxlength="20" value="{{old('email')}}"><br>
    @error('email')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    パスワード：<br>
    <input id="pass" type="password" name="pass" maxlength="32" value="{{old('pass')}}"><br>
    @error('pass')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    パスワードをもう一度入力してください：<br>
    <input id="pass2" type="password" name="pass2" maxlength="32" value="{{old('pass2')}}"><br>
    @error('pass2')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    <br>
    <input id="button" type="submit" name="submitbtn" value="ＯＫ">
</form>
@endsection