@extends('layouts.report_menu')

@section('title', 'シフト編集')

@section('content')
<!-- 成功メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        ※{{ session('success_message') }}
    </div>
@endif
<!-- エラーメッセージ -->
@if (session('error_message'))
    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
        ※{{ session('error_message') }}
    </div>
@endif

<div class="container">
    <form method="GET" action="{{ route('admin.shifts.edit') }}">
        @csrf
        【月】
        <input id="schmonth" type="month" name="schmonth" maxlength="10">
        @error('schmonth')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        【利用者ID】
        <input type="text" name="schuser_id" maxlength="10">
        @error('schuser_id')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        <br>
        <input type="submit" name="schsubmit" value="検索">
    </form>
    <br>
    @if (!empty($searchitem['user_name']))
        {{$searchitem['user_name']}}のシフト
    @else
        <div>全体シフト</div>
        <div>全体シフトの編集結果は表示されません。 個別のIDでご確認ください。</div>
    @endif
    <h3>{{$searchitem['html_title']}}</h3>
    <form method="POST" action="{{ route('admin.shifts.edit.post') }}">
        @csrf
        <input type="submit" name="editsubmit" value="保存"><br>
        <input type="hidden" name="schmonth" value="{{$searchitem['schmonth']}}">
        <input type="hidden" name="schuser_id" value="{{$searchitem['schuser_id']}}">
        【一括セット】
        <div>シフト：{!! pulldown_monthshift() !!}</div>
        @error('month_shift')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        <div>出勤時間<input type="time" name="month_arrivaltime" size="10"></div>
        @error('month_arrivaltime')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror
        <div>退勤時間<input type="time" name="month_leavetime" size="10"></div>
        @error('month_leavetime')
            <div style="color: red; font-size: 0.8em; margin-top: 5px;">
                {{ $message }}
            </div>
        @enderror

        @if ($errors->has('shift.*'))
            <div style="color: red; font-size: 0.8em; margin-bottom: 10px; font-weight: bold;">
                @foreach (collect($errors->get('shift.*'))->flatten() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif
        @if ($errors->has('arrivaltime.*'))
            <div style="color: red; font-size: 0.8em; margin-bottom: 10px; font-weight: bold;">
                @foreach (collect($errors->get('arrivaltime.*'))->flatten() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif
        @if ($errors->has('leavetime.*'))
            <div style="color: red; font-size: 0.8em; margin-bottom: 10px; font-weight: bold;">
                @foreach (collect($errors->get('leavetime.*'))->flatten() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif
        <table border="1" class="calender">
            <tr>
                <th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th>
            </tr>
            @foreach ($weeks as $week)
                {!! $week !!}
            @endforeach
        </table>
    </form>
</div>
@endsection