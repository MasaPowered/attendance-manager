@extends('layouts.report_menu')

@section('title', '管理者編集')

@section('content')

<!-- 登録成功時メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        {{ session('success_message') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.admins.edit_done') }}">
    @csrf
    管理者ID:
    <input type="hidden" name="id" value="{{$message_array->id}}">
    @error('id')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    {{$message_array->id}}<br>
    氏名:<br>
    @if($message_array->id == 1)
        <input type="hidden" name="name" value="{{$message_array->name}}">
        <small class="text-danger">※マスターアカウントのパスワード以外変更できません。</small><br>
    @else
        <input type="text" name="name" value="{{$message_array->name}}"><br>
    @endif
    @error('name')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    
    メールアドレス：<br>
    @if($message_array->id == 1)
        <input type="hidden" name="email" value="{{$message_array->email}}">
        <small class="text-danger">※マスターアカウントのパスワード以外変更できません。</small><br>
    @else
        <input id="email" type="text" name="email" maxlength="20" value="{{$message_array->email}}"><br>
    @endif
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