@extends('layouts.search_form')

@section('title', '業務報告書ダウンロード')

@section('content')
<p>検索結果をCSVファイルでダウンロードできます。</p>

<form method="POST" action="{{ route('admin.work_reports.download_done') }}">
    @csrf
    @if (!empty($message_array) && !empty($searchitem['schsubmit']))
        <div>{{$message_array->total()}}件</div><br>
        <input type="hidden" name="schdate" value="{{ $searchitem['schdate'] ?? '' }}">
        <input type="hidden" name="schmonth" value="{{ $searchitem['schmonth'] ?? '' }}">
        <input type="hidden" name="schuser_id" value="{{ $searchitem['schuser_id'] ?? '' }}">
        <input type="hidden" name="month_shift" value="{{ $searchitem['month_shift'] ?? '' }}">
        <input type="hidden" name="arriveradio" value="{{ $searchitem['arriveradio'] ?? '' }}">
        <input type="hidden" name="leaveradio" value="{{ $searchitem['leaveradio'] ?? '' }}">
        <input type="hidden" name="checkbox" value="{{ $searchitem['checkbox'] ?? '' }}">
        
        <input type="submit" value="CSVダウンロード">
        <div class="mt-4">
            {{ $message_array->withQueryString()->links() }}
        </div>
        <table border="1">
            <tr>
                <td>日付</td>
                <td>利用者ID</td>
                <td>名前</td>
                <td>シフト</td>
                <td>運刻</td>
                <td>出勤時報告 </td>
                <td>退勤時報告 </td>
            </tr>
            @foreach ($message_array as $value)
                <tr>
                    <td>{{ $value->date }}</td>
                    <td>{{ $value->user_id }}</td>
                    <td>{{ $value->name }}</td>
                    <td>{{ $value->shift_status }}</td>
                    <td>{{ $value->latetime ? date('H:i', strtotime($value->latetime)) : '' }}</td>
                    <td>{{ $value->startreport }}</td>
                    <td>{{ $value->endreport }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</form>
<script>
    let schdate = document.getElementById('schdate');
    let schmonth = document.getElementById('schmonth');
    schdate.addEventListener('click', (e) => {
        schmonth.value = '';
    })
    schmonth.addEventListener('click', (e) => {
        schdate.value = '';
    })
</script>
@endsection