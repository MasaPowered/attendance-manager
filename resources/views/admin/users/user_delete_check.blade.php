@extends('layouts.report_menu')

@section('title', '利用者削除')

@section('content')

<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message"><?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST" action="user_delete_done">
        @csrf
        <input type="hidden" name="id" value="<?php echo $message_array->id ?>">
        こちらの内容を削除してもよろしいでしょうか？：<input type="submit" value="OK">
        <table border="1">
            <tr>
                <td>利用者ID</td>
                <td>氏名</td>
            </tr>
            <tr>
                <td><?php echo $message_array->id ?></td>
                <td><?php echo $message_array->name ?></td>
            </tr>
        </table>
    </form>
<?php endif; ?>
@endsection