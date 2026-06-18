@extends('layouts.report_menu')

@section('title', '管理者削除')

@section('content')

<!-- 登録成功時メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        {{ session('success_message') }}
    </div>
@endif
@if (session('error_message'))
    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
        ※{{ session('error_message') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.admins.delete_check') }}">
    @csrf
    @if (session('error_general'))
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ session('error_general') }}
        </div>
    @endif
    @error('radio')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    選択された内容を削除しますか？：<input type="submit" name="delsubmit" value="削除">
    <table border="1">
        <tr>
            <td>選択</td>
            <td>管理者ID</td>
            <td>氏名</td>
            <td>メールアドレス</td>
        </tr>
        @if (!empty($message_array))
            @foreach ($message_array as $value)
                <tr>
                    <td>
                        @if($value->id != 1)
                            <input type="radio" name="radio" value="{{$value->id}}">
                        @else
                            <span class="badge bg-secondary">×</span>
                        @endif
                    </td>
                    <td>{{$value->id}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->email}}</td>
                </tr>
            @endforeach
        @endif
    </table>
</form>
@endsection