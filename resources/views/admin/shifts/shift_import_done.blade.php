@extends('layouts.report_menu')

@section('title', 'シフトインポート')

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
@endsection