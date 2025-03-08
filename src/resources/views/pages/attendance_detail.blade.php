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
            <form action="" method="">
                <table class="c-formTable">
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">名前</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content"><span class="c-formTable_td_item">西　伶奈</span></p>
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">日付</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content"><span class="c-formTable_td_item">2023年</span><span class="c-formTable_td_item">6月1日</span></p>
                            <p class="c-formTable_td_content"></p>
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">出勤・退勤</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_item"><input type="text" value="09:00"></span>
                                <span class="c-formTable_td_item">～</span>
                                <span class="c-formTable_td_item"><input type="text" value="18:00"></span>
                            </p>
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">休憩</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <span class="c-formTable_td_item"><input type="text" value="12:00"></span>
                                <span class="c-formTable_td_item">～</span>
                                <span class="c-formTable_td_item"><input type="text" value="13:00"></span>
                            </p>
                        </td>
                    </tr>
                    <tr class="c-formTable_tr">
                        <th class="c-formTable_th">備考</th>
                        <td class="c-formTable_td">
                            <p class="c-formTable_td_content">
                                <textarea name="" id="" cols="30" rows="5">電車遅延のため</textarea>
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="p-attendanceDetail_btn">
                    <button class="c-btn --small">修正</button>
                </div>
                <p class="p-attendanceDetail_note">*承認待ちのため修正はできません。</p>
            </form>
        </div>
    </div>
</div>
@endsection
