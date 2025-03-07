@extends('layouts.default')

@section('title','勤怠登録')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="c-content p-attendance">
    <span class="p-attendance_label">出勤中</span>
    <div class="p-attendance_date">2023年6月1日(木)</div>
    <div class="p-attendance_time">08:00</div>
    <div class="p-attendance_btns">
        <button class="p-attendance_btn c-btn">退勤</button>
        <button class="p-attendance_btn c-btn --reverse">休憩入</button>
    </div>
    <p class="p-attendance_note">お疲れ様でした。</p>
</div>
@endsection
