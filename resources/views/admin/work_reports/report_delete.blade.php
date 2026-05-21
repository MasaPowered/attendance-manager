@extends('layouts.delete_search_form')

@section('title', '業務報告削除')

@section('content')

<form method="POST" action="{{ route('admin.work_reports.delete_check') }}">
    @csrf
    <?php if (!empty($message_array) && !empty($searchitem['schsubmit'])) : ?>
        <?php echo $message_array->count() ?>件<br>
        選択された内容を削除しますか？<input type="submit" name="submithtn" value="削除">
        <table border="1">
            <tr>
                <td>選択</td>
                <td>日付</td>
                <td>利用者ID</td>
                <td>名前</td>
                <td>シフト</td>
                <td>出勤</td>
                <td>遅刻</td>
                <td>出勤時報告</td>
                <td>退勤</td>
                <td>勤時時報告</td>
            </tr>
            <?php $i = 0; ?>
            <?php foreach ($message_array as $value) : ?>
                <tr>
                    <td><input type="checkbox" name="checkbox[]" value="<?php echo $value->date . ',' . $value->user_id ?>"></td>
                    <td><?php echo $value->date ?></td>
                    <td><?php echo $value->user_id ?></td>
                    <td><?php echo $value->name ?></td>
                    <td><?php echo $value->shift_status ?></td>
                    <td><input type="checkbox" name="start" <?php if ($value->arrivalcheck) echo "checked" ?> disabled></td>
                    <td><?php if (!empty($value->latetime)) echo date('H:i', strtotime($value->latetime)); ?></td>
                    <td><?php echo $value->startreport ?></td>
                    <td><input type="checkbox" name="end" <?php if ($value->leavecheck) echo "checked" ?> disabled></td>
                    <td><?php echo $value->endreport ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</form>

<script>
    var schdate = document.getElementById('schdate');
    var schmonth = document.getElementById('schmonth');
    schdate.addEventListener('click', (e) => {
        schmonth.value = '';
    })
    schmonth.addEventListener('click', (e) => {
        schdate.value = '';
    })
</script>
@endsection