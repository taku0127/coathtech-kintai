@extends('layouts.default')

@section('title','勤怠登録')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/stamp_correction_request_list.css')}}">
@endpush
@section('content')
<div class="c-content p-stampCorrectionRequestList">
    <div class="c-layout">
        <h1 class="c-title">勤怠一覧</h1>
        <div class="p-stampCorrectionRequestList_links">
            <a href="{{ route('stamp_correction_request') }}" class="p-stampCorrectionRequestList_link{{ $pageParam == 'approval' ? "" : ' --active' }}">承認待ち</a>
            <a href="{{ route('stamp_correction_request',['page' => 'approval']) }}" class="p-stampCorrectionRequestList_link{{ $pageParam == 'approval' ? ' --active' : "" }}">承認済み</a>
        </div>
        <div class="c-tableBox p-stampCorrectionRequestList_table">
            <table class="c-table">
                <tr class="c-table_tr">
                    <th class="c-table_th">状態</th>
                    <th class="c-table_th">名前</th>
                    <th class="c-table_th">対象日時</th>
                    <th class="c-table_th">申請理由</th>
                    <th class="c-table_th">申請日時</th>
                    <th class="c-table_th">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                <tr class="c-table_tr">
                    <td class="c-table_td">{{ $pageParam == 'approval' ? "承認済み" : "承認待ち" }}</td>
                    <td class="c-table_td">{{ $attendance->user->name}}</td>
                    <td class="c-table_td">{{ $attendance->getDateFormatted('date') }}</td>
                    <td class="c-table_td">{{ $attendance->attendanceFix[0]->note }}</td>
                    <td class="c-table_td">{{ $attendance->attendanceFix[0]->getDateFormatted('created_at') }}</td>
                    <td class="c-table_td"><a href="{{ route('attendance_detail',['id' => $attendance->id]) }}" class="c-table_link">詳細</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
