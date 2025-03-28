@extends('layouts.default')

@section('title','勤怠一覧')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endpush
@section('content')
<div class="c-content p-attendanceList">
    <div class="c-layout">
        <h1 class="c-title">{{ $dateInfo['year'] }}年{{ $dateInfo['month'] }}月{{ $dateInfo['day'] }}日の勤怠</h1>
        <div class="c-pageNation">
            <a href="{{ route('admin.attendance_list',['page' => $page - 1]) }}" class="c-pageNation_link --prev">前日</a>
            <span class="c-pageNation_current">{{ $dateInfo['year'] }}/{{ $dateInfo['month'] }}/{{ $dateInfo['day'] }}</span>
            <a href="{{ route('admin.attendance_list',['page' => $page + 1]) }}" class="c-pageNation_link --next">翌日</a>
        </div>
        <div class="c-tableBox p-attendanceList_table">
            <table class="c-table">
                <tr class="c-table_tr">
                    <th class="c-table_th">名前</th>
                    <th class="c-table_th">出勤</th>
                    <th class="c-table_th">退勤</th>
                    <th class="c-table_th">休憩</th>
                    <th class="c-table_th">合計</th>
                    <th class="c-table_th">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                <tr class="c-table_tr">
                    <td class="c-table_td">{{ $attendance->user->name }}</td>
                    <td class="c-table_td">{{ $attendance->getTimeFormatted('clock_in') }}</td>
                    <td class="c-table_td">{{ $attendance->getTimeFormatted('clock_out') }}</td>
                    <td class="c-table_td">{{ $attendance->getBreakTimesShow() }}</td>
                    <td class="c-table_td">
                        @if ($attendance->clock_out)
                        {{ $attendance->getActualWorkTime() }}
                        @endif
                    </td>
                    <td class="c-table_td"><a href="{{ route('admin.attendance_detail',['id' => $attendance->id]) }}" class="c-table_link">詳細</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
