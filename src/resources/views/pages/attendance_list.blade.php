@extends('layouts.default')

@section('title','勤怠登録')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endpush
@section('content')
<div class="c-content p-attendanceList">
    <div class="c-layout">
        <h1 class="c-title">勤怠一覧</h1>
        <div class="c-pageNation">
            <a href="{{ route('user.attendance_list',['page' => $page - 1]) }}" class="c-pageNation_link --prev">前月</a>
            <span class="c-pageNation_current">{{ $year }}/{{ $month }}</span>
            <a href="{{ route('user.attendance_list',['page' => $page + 1]) }}" class="c-pageNation_link --next">翌月</a>
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
                    <td class="c-table_td">{{ $attendance->clock_in }}</td>
                    <td class="c-table_td">{{ $attendance->clock_out }}</td>
                    <td class="c-table_td">
                        @foreach ($attendance->breakTimes as $breakTime)
                            @if ($loop->index > 0)
                                <br>
                            @endif
                            {{ $breakTime->start}}~{{ $breakTime->end }}
                        @endforeach
                    </td>
                    <td class="c-table_td">
                        @if ($attendance->clock_out)
                        @php
                             // 出勤・退勤時間を Carbon インスタンス化
                            $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                            $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                            // 休憩時間の合計（分単位）
                            $breakMinutes = $attendance->breakTimes->filter(function($break){
                                return !is_null($break->end);
                            })->sum(function($break) {
                                $breakStart = \Carbon\Carbon::parse($break->start);
                                $breakEnd = \Carbon\Carbon::parse($break->end); return $breakEnd->diffInMinutes($breakStart);
                            });
                            $actualWorkMinutes = ($clockIn && $clockOut) ? $clockIn->diffInMinutes($clockOut) - $breakMinutes : 0;
                            $hours = floor($actualWorkMinutes / 60);
                            $minutes = $actualWorkMinutes % 60;
                        @endphp
                        {{ sprintf('%d:%02d', $hours, $minutes) }}
                        @endif
                    </td>
                    <td class="c-table_td"><a href="{{ route('user.attendance_detail',['id' => $attendance->id]) }}" class="c-table_link">詳細</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
