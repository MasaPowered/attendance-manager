@extends('layouts.report_menu')

@section('title', '管理者追加')

@section('content')

<!-- 登録成功時メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        {{ session('success_message') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.admins.add_check') }}">
    @csrf
    @if (session('error_general'))
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ session('error_general') }}
        </div>
    @endif
    氏名：<br>
    <input id="name" type="text" name="name" maxlength="255" value="{{old('name')}}"><br>
    @error('name')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    メールアドレス：<br>
    <input id="email" type="text" name="email" maxlength="255" value="{{old('email')}}"><br>
    @error('email')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    パスワード：<br>
    <input id="pass" type="password" name="pass" maxlength="255"><br>
    @error('pass')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    パスワードをもう一度入力してください：<br>
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