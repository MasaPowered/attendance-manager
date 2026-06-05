@extends('layouts.report_menu')

@section('title', '利用者一覧')

@section('content')

<p class="success_message">修正しました。</p>

<table border="1">
    <tr>
        <td>利用者ID</td>
        <td>名前</td>
        <td>メールアドレス</td>
    </tr>
    <tr>
        <td>{{$user->id}}</td>
        <td>{{$user->name}}</td>
        <td>{{$user->email}}</td>
    </tr>
</table>
@endsection