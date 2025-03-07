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
            <a href="#" class="c-pageNation_link --prev">前月</a>
            <span class="c-pageNation_current">2023/06</span>
            <a href="#" class="c-pageNation_link --next">翌月</a>
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
                <tr class="c-table_tr">
                    <td class="c-table_td">06/01(木)</td>
                    <td class="c-table_td">09:00</td>
                    <td class="c-table_td">18:00</td>
                    <td class="c-table_td">1:00</td>
                    <td class="c-table_td">8:00</td>
                    <td class="c-table_td"><a href="" class="c-table_link">詳細</a></td>
                </tr>
                <tr class="c-table_tr">
                    <td class="c-table_td">06/01(木)</td>
                    <td class="c-table_td">09:00</td>
                    <td class="c-table_td">18:00</td>
                    <td class="c-table_td">1:00</td>
                    <td class="c-table_td">8:00</td>
                    <td class="c-table_td"><a href="" class="c-table_link">詳細</a></td>
                </tr>
                <tr class="c-table_tr">
                    <td class="c-table_td">06/01(木)</td>
                    <td class="c-table_td">09:00</td>
                    <td class="c-table_td">18:00</td>
                    <td class="c-table_td">1:00</td>
                    <td class="c-table_td">8:00</td>
                    <td class="c-table_td"><a href="" class="c-table_link">詳細</a></td>
                </tr>
                <tr class="c-table_tr">
                    <td class="c-table_td">06/01(木)</td>
                    <td class="c-table_td">09:00</td>
                    <td class="c-table_td">18:00</td>
                    <td class="c-table_td">1:00</td>
                    <td class="c-table_td">8:00</td>
                    <td class="c-table_td"><a href="" class="c-table_link">詳細</a></td>
                </tr>
                <tr class="c-table_tr">
                    <td class="c-table_td">06/01(木)</td>
                    <td class="c-table_td">09:00</td>
                    <td class="c-table_td">18:00</td>
                    <td class="c-table_td">1:00</td>
                    <td class="c-table_td">8:00</td>
                    <td class="c-table_td"><a href="" class="c-table_link">詳細</a></td>
                </tr>
                <tr class="c-table_tr">
                    <td class="c-table_td">06/01(木)</td>
                    <td class="c-table_td">09:00</td>
                    <td class="c-table_td">18:00</td>
                    <td class="c-table_td">1:00</td>
                    <td class="c-table_td">8:00</td>
                    <td class="c-table_td"><a href="" class="c-table_link">詳細</a></td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
