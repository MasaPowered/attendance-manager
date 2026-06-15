@extends('layouts.report_menu')

@section('title', '利用者一覧')

@section('content')

<form method="GET" action="{{ route('admin.users.edit') }}">
    @csrf
    @error('radio')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    選択された内容を編集しますか?： <input type="submit" name="editsubmit" value="編集">
    <table border="1">
        <tr>
            <td>選択</td>
            <td>利用者ID</td>
            <td>氏名</td>
            <td>メールアドレス</td>
        </tr>
        @if ($message_array->isEmpty())
        @else
        @foreach ($message_array as $value)
        <tr>
            <td><input type="radio" name="radio" value="{{$value->id}}"></td>
            <td>{{$value->id}}</td>
            <td>{{$value->name}}</td>
            <td>{{$value->email}}</td>
        </tr>
        @endforeach
        @endif
    </table>
</form>
@endsection