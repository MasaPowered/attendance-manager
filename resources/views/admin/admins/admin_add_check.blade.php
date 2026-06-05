@extends('layouts.report_menu')

@section('title', '管理者追加')

@section('content')

こちらの内容を登録してもよろしいですか？：<br>
<form method="POST" action="{{ route('admin.admins.add_done') }}">
    @csrf
    <input type="hidden" name="name" value="{{ $data['name'] }}">
    <input type="hidden" name="email" value="{{ $data['email'] }}">
    氏名：{{ $data['name'] }}<br>
    メールアドレス：{{ $data['email'] }}<br>
    <br>
    <input id="button" type="submit" name="submitbtn" value="ＯＫ">
</form>
@endsection