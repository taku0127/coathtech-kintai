@extends('layouts.default')

@section('title','勤怠詳細')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endpush
@section('content')
<div class="c-content p-attendanceDetail">
    <div class="c-layout">
        <h1 class="c-title">勤怠詳細</h1>
        <div class="p-attendanceDetail_content">
            <form action="{{ route('admin.approve',['id' => $attendance->id]) }}" method="POST">
                @csrf
                <table class="c-formTable">
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">名前</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content"><span class="c-formTable_td_item">{{ $attendance->attendance->user->name }}</span></p>
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">日付</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_item">{{ \Carbon\Carbon::parse($attendance->attendance->date)->format('Y年') }}</span><span class="c-formTable_td_item">{{ \Carbon\Carbon::parse($attendance->attendance->date)->format('n月j日') }}</span></p>
                            <p class="c-formTable_td_content"></p>
                            @error('date')
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">出勤・退勤</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_item">
                                    {{ $attendance->getTimeFormatted('clock_in') }}
                                </span>
                                <span class="c-formTable_td_item">～</span>
                                <span class="c-formTable_td_item">
                                    {{ $attendance->getTimeFormatted('clock_out') }}
                                </span>
                            </p>
                            @error('clock_in')
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                            @error('clock_out')
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </td>
                    </tr>
                    @foreach ($attendance->breakTimeFixes as $breakTimeFixes)
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">休憩{{ $loop->iteration == 1 ? "" : $loop->iteration }}</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_item">
                                    {{ $breakTimeFixes->getTimeFormatted('start') }}
                                </span>
                                <span class="c-formTable_td_item">～</span>
                                <span class="c-formTable_td_item">
                                    {{ $breakTimeFixes->getTimeFormatted('end')}}
                                </span>
                            </p>
                            @error("break_time.start.{$breakTimeFixes->id}")
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                            @error("break_time.end.{$breakTimeFixes->id}")
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </td>
                    </tr>
                    @endforeach
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">備考</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_content_note">{{ $attendance->note }}</span>
                            </p>
                            @error("note")
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </td>
                    </tr>
                </table>
                <div class="p-attendanceDetail_btn">
                    <button class="c-btn --small" {{ $attendance->approval ? "disabled" : ""}}>{{ $attendance->approval ? "承認済み" : "承認"}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
