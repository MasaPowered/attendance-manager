@extends('layouts.search_form')

@section('title', '業務報告一覧')

@section('content')
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">*<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>
<br><br>

<form method="GET" action="{{ route('admin.work_reports.edit') }}">
    @csrf
    @error('radio')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    <?php if (!empty($message_array) && !empty($searchitem["schsubmit"])) : ?>
        <?php echo $message_array->count() ?>件<br>
        選択された内容を編集しますか？<input type="submit" name= "editsubmit" value="編集">
        <div class="mt-4">
            {{ $message_array->withQueryString()->links() }}
        </div>
        <table border="1">
            <tr>
                <td>選択</td>
                <td>日付</td>
                <td>利用者ID</td>
                <td>名前</td>
                <td>シフト</td>
                <td>遅刻</td>
                <td>出勤時報告</td>
                <td>退勤時報告</td>
            </tr>
            <?php $i = 0; ?>
            <?php foreach ($message_array as $value) : ?>
                <tr>
                    <td>
                        <?php if ($value->arrivalcheck || $value->leavecheck) : ?>
                            <input type="radio" name="radio" value="<?php echo $value->date . ',' . $value->user_id ?>">
                        <?php endif; ?>
                    </td>
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