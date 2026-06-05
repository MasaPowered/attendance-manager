@extends('layouts.report_menu')

@section('title', '利用者ログイン制限設定')

@section('content')

<!-- 登録成功時メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        {{ session('success_message') }}
    </div>
@endif

<form method="POST">
    @csrf
    @if ($errors->any())
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            <ul style="list-style-type: none; padding-left: 0;">
                @foreach ($errors->all() as $error)
                    <li>※{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <p>ログイン制限をかける場合チェックをつけてください。</p>
    【ログイン制限】
    <input type="checkbox" name="logintime_status" <?php if (!empty($message_array->logintime_status)) echo 'checked'; ?>>
    【開始時間】
    <input id="start_time" type="time" name="start_time" value="{{ $message_array->start_time ? date('H:i', strtotime($message_array->start_time)) : '' }}">
    ～
    【終了時間】
    <input id="end_time" type="time" name="end_time" value="{{ $message_array->end_time ? date('H:i', strtotime($message_array->end_time)) : '' }}">
    <br>
    <input type="submit" value="保存" name="updsubmit">
</form>
@endsection