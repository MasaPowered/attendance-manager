@extends('layouts.report_menu')

@section('title', 'シフトインポート')

@section('content')
<form method="POST" action="{{ route('admin.shifts.import_done') }}" enctype="multipart/form-data">
    @csrf
    ファイルを指定してください。<br>
    <input type="file" name="shift"><br>
    <input type="submit" value="ＯＫ">
</form>
<br>
※シフトのExcelファイルをcsvに変換し下記の用に修正してインポートしてください。<br>
<img src="{{ asset('img/shift_import.png') }}" alt="shift image" />
@endsection