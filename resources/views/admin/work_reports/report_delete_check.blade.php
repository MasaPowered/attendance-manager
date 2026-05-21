@extends('layouts.report_menu')

@section('title', '業務報告削除')

@section('content')
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST" action="report_delete_done">
        @csrf
        削除してもよろしいですか？<input type="submit" value="削除" name="submitbtn">
        <table border="1">
            <tr>
                <td>日付</td>
                <td>利用者ID</td>
                <td>名前</td>
                <td>シフト</td>
                <td>出勤</td>
                <td>遅刻</td>
                <td>出勤時報告</td>
                <td>退勤</td>
                <td>退勤時報告</td>
            </tr>
            <?php $i = 0; ?>
            <?php foreach ($message_array as $value) : ?>
                <tr>
                    <input type="hidden" name="arriveid[]" value="<?php echo $value->arriveid; ?>">
                    <input type="hidden" name="leaveid[]" value="<?php echo $value->leaveid; ?>">
                    <input type="hidden" name="user_id[]" value="<?php echo $value->user_id; ?>">
                    <input type="hidden" name="date[]" value="<?php echo $value->date; ?>">
                    <input type="hidden" name="startreport[]" value="<?php echo $value->startreport; ?>">
                    <input type="hidden" name="endreport[]" value="<?php echo $value->endreport; ?>">
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
    </form>
<?php endif; ?>
@endsection