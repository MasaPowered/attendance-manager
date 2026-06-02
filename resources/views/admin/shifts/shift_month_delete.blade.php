@extends('layouts.report_menu')

@section('title', 'シフト一括削除')

@section('content')
<!-- 登録成功時メッセージ -->
<?php if (!empty($success_message)) : ?>
    <div class="success_message"><?php echo $success_message; ?></div>
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
        <input id="schmonth" type="month" name="schmonth" maxlength="10"> <br>
        @error('schmonth')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        <input type="submit" name="schsubmit" value="検索">
    </form>
    <h3><?php echo $searchitem['html_title']; ?></h3>
    <form method="POST" action="{{ route('admin.shifts.delete_check') }}">
        @csrf
        <?php if (!empty($message_array)) : ?>
            <input type="submit" name="delsubmit" value="削除" {{ $message_array->isEmpty() ? 'disabled' : '' }}>
            <input type="hidden" name="schmonth" value="<?php echo $searchitem['schmonth'] ?>">
            <table border="1">
                <tr>
                    <td>利用者ID</td>
                    <td>利用者名 </td>
                    <?php for ($day = 1; $day <= $searchitem['day_count']; $day++) : ?>
                        <td><?php echo $day ?>日</td>
                    <?php endfor; ?>
                </tr>
                <?php $prev_user_id = ''; ?>
                <?php for ($day = 1, $row = 0; $day <= $searchitem['day_count']; $day++) : ?>
                    <?php $date = $searchitem['schmonth'] . (($day < 10) ? '-0' : '-') . $day; ?>
                    <?php if (!empty($message_array[$row]) && strcmp($prev_user_id, $message_array[$row]->user_id)) : ?>
                        <?php if ($prev_user_id != '') : ?>
                            <?php $day = 1; ?>
                            <?php $date = $searchitem['schmonth'] . (($day < 10) ? '-0' : '-') . $day; ?>
                        <?php endif; ?>
                        <?php $prev_user_id = $message_array[$row]->user_id; ?>
                        <tr>
                            <td><?php echo $message_array[$row]->user_id ?></td>
                            <td><?php echo $message_array[$row]->name ?></td>
                        <?php endif; ?>
                        <?php if (!empty($message_array[$row]) && $message_array[$row]->date == $date) : ?>
                            <td><?php echo $message_array[$row]->shift_status ?></td>
                            <?php $row++ ?>
                            <?php if (!empty($message_array[$row]->user_id) && $day == $searchitem['day_count']) : ?>
                                <?php $day = 0; ?>
                            <?php endif; ?>
                        <?php else : ?>
                            <td></td>
                        <?php endif; ?>
                    <?php endfor; ?>
                        </tr>
            </table>
        <?php endif; ?>
    </form>
</div>
@endsection