@extends('layouts.report_menu')

@section('title', '管理者編集')

@section('content')

<p class="success_message">修正しました。</p>

<table border="1">
    <tr>
        <td>管理者ID</td>
        <td>名前</td>
        <td>メールアドレス</td>
    </tr>
        <tr>
            <td>{{$admin->id}}</td>
            <td>{{$admin->name}}</td>
            <td>{{$admin->email}}</td>
        </tr>
</table>
@endsection