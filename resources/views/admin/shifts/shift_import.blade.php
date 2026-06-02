@extends('layouts.report_menu')

@section('title', 'シフトインポート')

@section('content')
@if ($errors->has('csv_errors'))
    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
        <h4>⚠️ CSVファイルの内容にエラーがあります（インポートは中止されました）</h4>
        <ul>
            @foreach ($errors->get('csv_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="{{ route('admin.shifts.import_done') }}" enctype="multipart/form-data">
    @csrf
    ファイルを指定してください。<br>
    <input type="file" name="csv_file"><br>
    <input type="submit" value="ＯＫ">
</form>
<br>
※シフトのExcelファイルをcsvに変換し下記の用に修正してインポートしてください。<br>
<img src="{{ asset('img/shift_import.png') }}" alt="shift image" />
@endsection