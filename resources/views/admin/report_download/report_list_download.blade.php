@extends('layouts.search_form')

@section('title', '業務報告書ダウンロード')

@section('content')
<p>検索結果をCSVファイルでダウンロードできます。</p>

<form method="POST" action="{{ route('admin.work_reports.download_done') }}">
    @csrf
    <?php if (!empty($message_array) && !empty($searchitem['schsubmit'])) : ?>
        <?php echo $message_array->count() ?>件<br>
        <input type="hidden" name="schdate" value="<?php if (!empty($searchitem['schdate'])) echo $searchitem['schdate']; ?>">
        <input type="hidden" name="schmonth" value="<?php if (!empty($searchitem['schmonth'])) echo $searchitem['schmonth']; ?>">
        <input type="hidden" name="schuser_id" value="<?php if (!empty($searchitem['schuser_id'])) echo $searchitem['schuser_id']; ?>">
        <input type="hidden" name="month_shift" value="<?php if (!empty($searchitem['month_shift'])) echo $searchitem['month_shift']; ?>">
        <input type="hidden" name="arriveradio" value="<?php if (!empty($searchitem['arriveradio'])) echo $searchitem['arriveradio']; ?>">
        <input type="hidden" name="leaveradio" value="<?php if (!empty($searchitem['leaveradio'])) echo $searchitem['leaveradio']; ?>">
        <input type="hidden" name="checkbox" value="<?php if (!empty($searchitem['checkbox'])) echo $searchitem['checkbox']; ?>">
        <input type="submit" value="CSVダウンロード" <?php if (empty($searchitem['schsubmit'])) echo "disabled" ?>>
        <table border="1">
            <tr>
                <td>日付</td>
                <td>利用者ID</td>
                <td>名前</td>
                <td>シフト</td>
                <td>運刻</td>
                <td>出勤時報告 </td>
                <td>退勤時報告 </td>
            </tr>
            <?php $i = 0; ?>
            <?php foreach ($message_array as $value) : ?>
                <tr>
                    <td><?php echo $value->date ?></td>
                    <td><?php echo $value->user_id ?></td>
                    <td><?php echo $value->name ?></td>
                    <td><?php echo $value->shift_status ?></td>
                    <td><?php if (!empty($value->latetime)) echo date('H:i', strtotime($value->latetime)); ?></td>
                    <td><?php echo $value->startreport ?></td>
                    <td><?php echo $value->endreport ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</form>
<script>
    let schdate = document.getElementById('schdate');
    let schmonth = document.getElementById('schmonth');
    schdate.addEventListener('click', (e) => {
        schmonth.value = '';
    })
    schmonth.addEventListener('click', (e) => {
        schdate.value = '';
    })
</script>
@endsection