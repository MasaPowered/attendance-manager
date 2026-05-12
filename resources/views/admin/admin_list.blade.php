@extends('layouts.report_menu')

@section('title', '管理者一覧')

@section('content')

<form method="POST" action="admin_edit">
    @csrf
    選択された内容を編集しますか？：<input type="submit" value="編集">
    <table border="1">
        <tr>
            <td>選択</td>
            <td>管理者ID</td>
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