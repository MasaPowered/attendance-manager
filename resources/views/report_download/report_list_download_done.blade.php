@extends('layouts.report_menu')

@section('title', '業務報告書ダウンロード')

@section('content')
<?php if (!empty($error_message)) : ?>
    <!-- エラーメッセージ -->
    <?php foreach ($error_message as $value) : ?>
        <div class="error_message">※<?php echo $value; ?></div>
    <?php endforeach; ?>
    <a href="work_report.csv">ダウンロード</a>
<?php endif; ?>
@endsection