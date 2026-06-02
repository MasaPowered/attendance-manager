@extends('layouts.report_menu')

@section('title', '管理者削除')

@section('content')
<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($message_array)) : ?>
    <form method="POST" action="{{ route('admin.admins.delete_done') }}">
        @csrf
        <input type="hidden" name="id" value="<?php echo $message_array->id ?>">
        @error('id')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        こちらの内容を削除してもよろしいでしょうか？：<input type="submit" value="ＯＫ">
        <table border="1">
            <tr>
                <td>管理者ID</td>
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