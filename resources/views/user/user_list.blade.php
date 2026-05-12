@extends('layouts.report_menu')

@section('title', '利用者一覧')

@section('content')

<form method="POST" action="user_edit">
    @csrf
    選択された内容を編集しますか?： <input type="submit" value="編集">
    <table border="1">
        <tr>
            <td>選択</td>
            <td>利用者ID</td>
            <td>氏名</td>
        </tr>
        @if ($message_array->isEmpty())
        @else
        @foreach ($message_array as $value)
        <tr>
            <td><input type="radio" name="radio" value=<?php echo $value->id ?>></td>
            <td>{{$value->id}}</td>
            <td>{{$value->name}}</td>
        </tr>
        @endforeach
        @endif
    </table>
</form>
@endsection