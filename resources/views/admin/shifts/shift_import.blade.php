@extends('layouts.report_menu')

@section('title', 'シフトインポート')

@section('content')
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        ※{{ session('success_message') }}
    </div>
@endif
<!-- エラーメッセージ -->
@if (session('error_message'))
    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
        ※{{ session('error_message') }}
    </div>
@endif
@if ($errors->has('csv_errors'))
    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
        <h4>CSVファイルの内容にエラーがあります（インポートは中止されました）</h4>
        <ul>
            @foreach ($errors->get('csv_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="{{ route('admin.shifts.import.post') }}" enctype="multipart/form-data">
    @csrf
    ファイルを指定してください。<br>
    <input type="file" name="csv_file"><br>
    <input type="submit" name="impsubmit" value="ＯＫ">
</form>
<?php if (!empty($message_array)) : ?>
    <h3><?php echo date('Y年m月', $searchitem["timestamp"]); ?></h3>
    <table border="1">
        <tr>
            <td>利用者ID</td>
            <td>利用者名</td>
            <?php for ($day = 1; $day <= $searchitem["day_count"]; $day++) : ?>
                <td><?php echo $day ?>日</td>
            <?php endfor; ?>
        </tr>
        <?php $prev_userid = ""; ?>
        <?php foreach ($message_array as $value) : ?>
            <?php if (strcmp($prev_userid, $value->user_id)) : ?>
                <?php if ($prev_userid != '') : ?>
                    </tr>
                <?php endif; ?>
                <?php $prev_userid = $value->user_id; ?>
                <tr>
                    <td><?php echo $value->user_id ?></td>
                    <td><?php echo $value->name ?></td>
                <?php endif; ?>
                <td><?php echo $value->shift_status ?></td>
            <?php endforeach; ?>
            <?php if (!empty($message_array)) : ?>
                </tr>
            <?php endif; ?>
    </table>
<?php endif; ?>
<br>
※シフトのExcelファイルをcsvに変換し下記の用に修正してインポートしてください。<br>
<img src="{{ asset('img/shift_import.png') }}" alt="shift image" />
@endsection