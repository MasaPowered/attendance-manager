@extends('layouts.report_menu')

@section('title', '利用者削除')

@section('content')

<form method="POST" action="{{ route('admin.users.delete_done') }}">
    @csrf
    <input type="hidden" name="id" value="{{$user->id}}">
    こちらの内容を削除してもよろしいでしょうか？：<input type="submit" value="OK">
    <table border="1">
        <tr>
            <td>利用者ID</td>
            <td>氏名</td>
        </tr>
        <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->name}}</td>
        </tr>
    </table>
</form>
@endsection