@extends('layouts.report_menu')

@section('title', 'パスワード変更')

@section('content')

<!-- 登録成功時メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        {{ session('success_message') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.password.update') }}">
    @csrf
    @method('PUT')
    新しいパスワードを入力してください：<br>
    <input id="pass" type="password" name="pass" maxlength="255"><br>
    @error('pass')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    新しいパスワードをもう一度入力してください：<br>
    <input id="pass2" type="password" name="pass2" maxlength="255"><br>
    @error('pass2')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    <br>
    <input id="button" type="submit" name="submitbtn" value="ＯＫ">
</form>
@endsection