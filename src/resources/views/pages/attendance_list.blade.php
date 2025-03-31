@extends('layouts.default')

@section('title','勤怠登録')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endpush
@section('content')
<div class="c-content p-attendanceList">
    <div class="c-layout">
        <h1 class="c-title">{{ $title ?? '勤怠一覧' }}</h1>
        <div class="c-pageNation">
            <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="c-pageNation_link --prev">前月</a>
            <span class="c-pageNation_current">{{ $year }}/{{ $month }}</span>
            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="c-pageNation_link --next">翌月</a>
        </div>
        <div class="c-tableBox p-attendanceList_table">
            <table class="c-table">
                <tr class="c-table_tr">
                    <th class="c-table_th">日付</th>
                    <th class="c-table_th">出勤</th>
                    <th class="c-table_th">退勤</th>
                    <th class="c-table_th">休憩</th>
                    <th class="c-table_th">合計</th>
                    <th class="c-table_th">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                <tr class="c-table_tr">
                    <td class="c-table_td">{{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }}({{ ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::parse($attendance->date)->dayOfWeek] }})</td>
                    <td class="c-table_td">{{ $attendance->getTimeFormatted('clock_in') }}</td>
                    <td class="c-table_td">{{ $attendance->getTimeFormatted('clock_out') }}</td>
                    <td class="c-table_td">
                        {{ $attendance->getBreakTimesShow() }}
                    </td>
                    <td class="c-table_td">
                        @if ($attendance->clock_out)
                        {{ $attendance->getActualWorkTime() }}
                        @endif
                    </td>
                    <td class="c-table_td"><a href="{{ route('attendance_detail',['id' => $attendance->id]) }}" class="c-table_link">詳細</a></td>
                </tr>
                @endforeach
            </table>
        </div>
        @if (Auth::guard('admin')->check())
        <div class="p-attendanceList_btn">
            <a class="c-btn --small" href="{{ route('exportCsv',['id' => $user->id ,'page' => $page]) }}">CSV出力</a>
        </div>
        @endif
    </div>
</div>
@endsection
