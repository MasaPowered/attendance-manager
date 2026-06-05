@extends('layouts.report_menu')

@section('title', '管理者削除')

@section('content')

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
        </tr>
        @if (!empty($message_array))
            @foreach ($message_array as $value)
                <tr>
                    <td><input type="radio" name="radio" value="{{$value->id}}"></td>
                    <td>{{$value->id}}</td>
                    <td>{{$value->name}}</td>
                </tr>
            @endforeach
        @endif
    </table>
</form>
@endsection