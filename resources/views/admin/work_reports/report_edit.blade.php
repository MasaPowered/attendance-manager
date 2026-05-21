@extends('layouts.report_menu')

@section('title', '業務報告編集')

@section('content')
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST" action="{{ route('admin.work_reports.edit_done') }}">
        @csrf
        <input type="hidden" name="arriveid" value="<?php echo $message_array->arriveid; ?>">
        <input type="hidden" name="leaveid" value="<?php echo $message_array->leaveid; ?>">
        <input type="hidden" name="user_id" value="<?php echo $message_array->user_id; ?>">
        <input type="hidden" name="date" value="<?php echo $message_array->date; ?>">
        【日付】
        <input id="schdate" type="date" name="date" maxlength="10" value="<?php echo $message_array->date ?>"><br>
        【利用者ID】
        <?php echo $message_array->userid ?><br>
        【氏名】
        <?php echo $message_array->name ?><br>
        【出勤報告】
        <?php if ($message_array->arrivalcheck) : ?>
            <?php if (!empty($message_array->arrivaltime)) : ?>
                <input type="time" name="arrivaltime" value="<?php echo substr($message_array->arrivaltime, 0, 8); ?>">
            <?php else : ?>
                <input type="time" name="arrivaltime">
            <?php endif; ?>
            <br>
            遅刻の場合遅刻時間入力：
            <input id="latetime" type="time" name="latetime" value="<?php echo substr($message_array->latetime, 0, 8); ?>" <?php if (!$message_array->arrivalcheck) echo "disabled"; ?>>
            <br>
            出勤業務内容：<br>
            <textarea id="starttextarea" name="startreport" class="commentTextArea" <?php if (!$message_array->arrivalcheck) echo "disabled"; ?>><?php echo $message_array->startreport ?></textarea>
        <?php else : ?>
            業務報告がされていないので編集できません。
        <?php endif; ?>
        <br>
        【退勤報告】
        <?php if ($message_array->leavecheck) : ?>
            <?php if (!empty($message_array->leavetime)) : ?>
                <input type="time" name="leavetime" value="<?php echo substr($message_array->leavetime, 0, 8); ?>">
            <?php else : ?>
                <input type="time" name="leavetime">
            <?php endif; ?>
            <br>
            退勤業務内容：<br>
            <textarea id="endtextarea" name="endreport" class="commentTextArea" <?php if (!$message_array->leavecheck) echo "disabled"; ?>><?php echo $message_array->endreport ?></textarea>
        <?php else : ?>
            業務報告がされていないので編集できません。
        <?php endif; ?>
        <br>
        <input type="submit" value="保存" name="submitbtn">
    </form>
<?php endif; ?>
<script>
    var startcheck = document.getElementById('startcheck');
    var endcheck = document.getElementById('endcheck');
    var userid = document.getElementById('user_id');
    var latetime = document.getElementById('latetime');
    var button = document.getElementById('button');
    //出勤が選ばれた時だけ遅刻時間を入力できる
    startcheck.addEventListener('click', (e) => {
        latetime.disabled = false;
    })

    endcheck.addEventListener('click', (e) => {
        latetime.disabled = true;
    })

    //ユーザーIDが入力されたらOK ボタンが押せる
    /*userid.addEventListener('keydown', (e) => {
        if (e.target.value.length >= 1) {
            button.disabled = false;
        } else {
            button.disabled = true;
        }
    }) */
</script>
@endsection