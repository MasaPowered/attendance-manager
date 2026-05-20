@extends('layouts.report_menu')

@section('title', '利用者削除')

@section('content')

<form method="POST" action="user_delete_check">
    @csrf
    選択された内容を削除しますか？：<input type="submit" value="削除">
    <table border="1">
        <tr>
            <td>選択</td>
            <td>利用者ID</td>
            <td>氏名</td>
        </tr>
        <?php if (!empty($message_array)) : ?>
            <?php $i = 0; ?>
            <?php foreach ($message_array as $value) : ?>
                <tr>
                    <td><input type="radio" name="radio" value="<?php echo $value->id ?>"></td>
                    <td><?php echo $value->id ?></td>
                    <td><?php echo $value->name ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</form>
@endsection