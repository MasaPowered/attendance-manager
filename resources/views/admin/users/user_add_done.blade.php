@extends('layouts.report_menu')

@section('title', '利用者追加')

@section('content')

<p class="success_message">追加しました。</p>

氏名：{{$data["name"]}}<br>
メールアドレス：{{$data["email"]}}<br>
<br>
@endsection