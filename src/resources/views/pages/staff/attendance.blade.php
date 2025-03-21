@extends('layouts.default')

@section('title','勤怠登録')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush
@push('js')
    <script src="js/script.js" defer></script>
@endpush

@section('content')
<div class="c-content p-attendance">
    <span class="p-attendance_label">{{ $attendance ? '出勤中' : '出勤外'  }}</span>
    <div class="p-attendance_date js-currentDay"></div>
    <div class="p-attendance_time js-currentTime"></div>
    <div class="p-attendance_btns">
        @if (!$attendance)
            <form action="{{ route('user.attendance') }}" method="POST">
                @csrf
                <input type="hidden" class="js-currentDay" name="date" value="">
                <input type="hidden" name="clock_in" class="js-currentTime" value="">
                <button class="p-attendance_btn c-btn">出勤</button>
            </form>
        @elseif($isBreak)
        <form action="{{ route('user.break_end') }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="end" class="js-currentTime" value="">
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
            <button class="p-attendance_btn c-btn --reverse">休憩戻り</button>
        </form>
        @else
            <button class="p-attendance_btn c-btn">退勤</button>
            <form action="{{ route('user.break_start') }}" method="POST">
                @csrf
                <input type="hidden" name="start" class="js-currentTime" value="">
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <button class="p-attendance_btn c-btn --reverse">休憩入</button>
            </form>
        @endif
    </div>
    {{-- <p class="p-attendance_note">お疲れ様でした。</p> --}}
</div>
@endsection
