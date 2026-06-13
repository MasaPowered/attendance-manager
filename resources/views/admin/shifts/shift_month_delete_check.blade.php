@extends('layouts.report_menu')

@section('title', 'シフト一括削除')

@section('content')

<form method="POST" action="{{ route('admin.shifts.delete_done') }}">
    @csrf
    <input type="hidden" name="schmonth" value="{{$searchitem['schmonth']}}">
    
    {{$searchitem["schmonth"]}}のシフトを削除してもよろしいですか？：<input type="submit" name="deletesubmit" value="OK">
</form>
@endsection