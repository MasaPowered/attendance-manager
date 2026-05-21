@extends('layouts.report_menu')

@section('title', 'シフト一括削除')

@section('content')

<?php if (!empty($searchitem["schmonth"])) : ?>
    <form method="POST" action="{{ route('admin.shifts.delete_done') }}">
        @csrf
        <input type="hidden" name="schmonth" value="<?php echo $searchitem["schmonth"] ?>">
        <?php echo $searchitem["schmonth"] ?>のシフトを削除してもよろしいですか？：<input type="submit" name="deletesubmit" value="OK">
    </form>
<?php endif; ?>
@endsection