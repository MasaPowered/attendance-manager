@extends('layouts.report_menu')

@section('title', 'シフト編集')

@section('content')
<!-- 成功メッセージ -->
<?php if (!empty($success_message)) : ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="container">
    <form method="POST">
        @csrf
        【月】
        <input id="schmonth" type="month" name="schmonth" maxlength="10">
        【利用者ID】
        <input type="text" name="schuser_id" maxlength="10">
        <br>
        <input type="submit" name="schsubmit" value="検索">
    </form>
    <br>
    <?php if (!empty($searchitem['user_name'])) : ?>
        <?php echo $searchitem['user_name'] ?>のシフト
    <?php else : ?>
        <div>全体シフト</div>
        <div>全体シフトの編集結果は表示されません。 個別のIDでご確認ください。</div>
    <?php endif; ?>
    <h3><?php echo $searchitem['html_title']; ?></h3>
    <form method="POST">
        @csrf
        <input type="submit" name="editsubmit" value="保存"><br>
        <input type="hidden" name="schmonth" value="<?php echo $searchitem['schmonth'] ?>">
        <input type="hidden" name="schuser_id" value="<?php echo $searchitem['schuser_id'] ?>">
        【一括セット】
        <div>シフト：<?php pulldown_monthshift(); ?></div>
        <div>出勤時間<input type="time" name="month_arrivaltime" size="10"></div>
        <div>退勤時間<input type="time" name="month_leavetime" size="10"></div>
        <table border="1">
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php
            foreach ($weeks as $week) {
                echo $week;
            }
            ?>
        </table>
    </form>
</div>
@endsection