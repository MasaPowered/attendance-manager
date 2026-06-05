@extends('layouts.report_menu')

@section('title', '管理者削除')

@section('content')

@if (!empty($admin))
    <form method="POST" action="{{ route('admin.admins.delete_done') }}">
        @csrf
        <input type="hidden" name="id" value="{{$admin->id}}">
        こちらの内容を削除してもよろしいでしょうか？：<input type="submit" value="ＯＫ">
        <table border="1">
            <tr>
                <td>管理者ID</td>
                <td>氏名</td>
            </tr>
            <tr>
                <td>{{$admin->id}}</td>
                <td>{{$admin->name}}</td>
            </tr>
        </table>
    </form>
@endif
@endsection