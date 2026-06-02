@extends('layouts.report_menu')

@section('title', '管理者追加')

@section('content')

<!-- エラーメッセージ -->
<?php if (!empty($error_message)) : ?>
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
<?php else : ?>
    こちらの内容を登録してもよろしいですか？：<br>
    <form method="POST" action="{{ route('admin.admins.add_done') }}">
        @csrf
        <input type="hidden" name="name" value=<?php echo $data["name"] ?>>
        <input type="hidden" name="email" value=<?php echo $data["email"] ?>>
        氏名：<?php echo $data["name"] ?><br>
        メールアドレス：<?php echo $data["email"] ?><br>
        <br>
        <input id="button" type="submit" name="submitbtn" value="ＯＫ">
    </form>
<?php endif; ?>
@endsection