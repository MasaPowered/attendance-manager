@extends('layouts.report_menu')

@section('title', 'シフト一括削除')

@section('content')
<!-- 登録成功時メッセージ -->
@if (session('success_message'))
    <div style="color: blue; font-size: 0.8em; margin-top: 5px;">
        ※{{ session('success_message') }}
    </div>
@endif
<!-- エラーメッセージ -->
@if (session('error_general'))
    <div style="color: red; font-size: 0.8em; margin-top: 5px;">
        {{ session('error_general') }}
    </div>
@endif
<form method="POST">
    @csrf
    【月】
    <input id="schmonth" type="month" name="schmonth" maxlength="10" value="{{$searchitem['schmonth']}}"> <br>
    @error('schmonth')
        <div style="color: red; font-size: 0.8em; margin-top: 5px;">
            {{ $message }}
        </div>
    @enderror
    <input type="submit" name="schsubmit" value="検索">
</form>

<form method="POST" action="{{ route('admin.shifts.delete_check') }}">
    @csrf
    @if (empty($formattedShifts))
        <div>シフトはありません。</div>
    @else
        <h3>{{$searchitem['html_title']}}</h3>
        <input type="submit" name="delsubmit" value="削除">
        <input type="hidden" name="schmonth" value="{{$searchitem['schmonth']}}">
        <table border="1">
            <tr>
                <td>利用者ID</td>
                <td>利用者名 </td>
                @for ($day = 1; $day <= $searchitem['day_count']; $day++)
                    <td>{{$day}}日</td>
                @endfor
            </tr>
            @foreach ($formattedShifts as $userId => $userData)
                <tr>
                    <td>{{ $userId }}</td>
                    <td>{{ $userData['name'] }}</td>
                    
                    @for ($day = 1; $day <= $searchitem['day_count']; $day++)
                        <td>{{ $userData['days'][$day] ?? '' }}</td>
                    @endfor
                </tr>
            @endforeach

            
        </table>
    @endif
</form>
@endsection