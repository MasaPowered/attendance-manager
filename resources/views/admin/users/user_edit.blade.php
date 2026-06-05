@extends('layouts.report_menu')

@section('title', '利用者一覧')

@section('content')

<form method="POST" action="{{ route('admin.users.edit_done') }}">
    @csrf
    利用者ID:
    <input type="hidden" name="id" value="{{$message_array->id}}">
    @error('id')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    {{$message_array->id}}<br>
    氏名:<br>
    <input type="text" name="name" value="{{$message_array->name}}"><br>
    @error('name')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    メールアドレス：<br>
    <input id="email" type="text" name="email" maxlength="20" value="{{$message_array->email}}"><br>
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
@endsection