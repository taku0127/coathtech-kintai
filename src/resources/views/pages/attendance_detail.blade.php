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
            <form action="{{ Route::currentRouteName() != 'admin.approve' ? route('attendance_detail',['id' => $attendance->id]) : route('admin.approve',['id' => $attendance->id]) }}" method="POST">
                @csrf
                <table class="c-formTable">
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">名前</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content"><span class="c-formTable_td_item">{{ $attendance->user->name }}</span></p>
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">日付</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                @if (Auth::guard('admin')->check() && Route::currentRouteName() != 'admin.approve')
                                <span class="c-formTable_td_item --wide">
                                    <input type="date" name="date" value="{{ old('date') ??$attendance->date }}">
                                </span>

                                @else
                                <span class="c-formTable_td_item">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span><span class="c-formTable_td_item">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span></p>
                                @endif
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
                                    @if (Route::currentRouteName() != 'admin.approve' && ($attendance->approval || Auth::guard('admin')->check()) )
                                    <input name="clock_in" type="text" value="{{ old('clock_in') ??$attendance->getTimeFormatted('clock_in') }}">
                                    @else
                                    {{ $attendance->attendanceFix[0]->getTimeFormatted('clock_in') }}
                                    @endif
                                </span>
                                <span class="c-formTable_td_item">～</span>
                                <span class="c-formTable_td_item">
                                    @if (Route::currentRouteName() != 'admin.approve' && ($attendance->approval || Auth::guard('admin')->check()) )
                                    <input name="clock_out" type="text" value="{{ old('clock_out') ?? $attendance->getTimeFormatted('clock_out'); }}">
                                    @else
                                    {{ $attendance->attendanceFix[0]->getTimeFormatted('clock_out') }}
                                    @endif
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
                    @foreach ($attendance->breakTimes as $breakTime)
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">休憩{{ $loop->iteration == 1 ? "" : $loop->iteration }}</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_item">
                                    @if (Route::currentRouteName() != 'admin.approve' && ($attendance->approval || Auth::guard('admin')->check()) )
                                    <input name="break_time[start][{{ $breakTime->id }}]" type="text" value="{{ old("break_time.start.{$breakTime->id}") ?? $breakTime->getTimeFormatted('start')}}">
                                    @else
                                        @if ($breakTime->breakTimeFix->isNotEmpty())
                                        {{ $breakTime->breakTimeFix[0]->getTimeFormatted('start') }}
                                        @endif
                                    @endif
                                </span>
                                <span class="c-formTable_td_item">～</span>
                                <span class="c-formTable_td_item">
                                    @if ( Route::currentRouteName() != 'admin.approve' && ($attendance->approval || Auth::guard('admin')->check()) )
                                    <input name="break_time[end][{{ $breakTime->id }}]" type="text" value="{{  old("break_time.end.{$breakTime->id}") ?? $breakTime->getTimeFormatted('end') }}">
                                    @else
                                        @if ($breakTime->breakTimeFix->isNotEmpty())
                                        {{ $breakTime->breakTimeFix[0]->getTimeFormatted('end')}}
                                        @endif
                                    @endif
                                </span>
                            </p>
                            @error("break_time.start.{$breakTime->id}")
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                            @error("break_time.end.{$breakTime->id}")
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
                                @if ( Route::currentRouteName() != 'admin.approve' && ($attendance->approval || Auth::guard('admin')->check()) )
                                <textarea name="note" id="" cols="30" rows="5">{{ old('note') }}</textarea>
                                @else
                                {{ $attendance->attendanceFix[0]->note }}
                                @endif
                            </p>
                            @error("note")
                                <p class="c-formTable_error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </td>
                    </tr>
                </table>
                @if (Route::currentRouteName() != 'admin.approve' && ($attendance->approval || Auth::guard('admin')->check()) )
                <div class="p-attendanceDetail_btn">
                    <button class="c-btn --small">修正</button>
                </div>
                @elseif(Route::currentRouteName() == 'admin.approve')
                <div class="p-attendanceDetail_btn">
                    <button class="c-btn --small" {{ $attendance->approval ? "disabled" : ""}}>{{ $attendance->approval ? "承認済み" : "承認"}}</button>
                </div>
                @else
                <p class="p-attendanceDetail_note">*承認待ちのため修正はできません。</p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
