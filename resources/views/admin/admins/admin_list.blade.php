@extends('layouts.report_menu')

@section('title', '管理者一覧')

@section('content')

<form method="GET" action="{{ route('admin.admins.edit') }}">
    @csrf
    @error('radio')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    選択された内容を編集しますか？：<input type="submit" name="editsubmit" value="編集">
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