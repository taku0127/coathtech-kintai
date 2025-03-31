@extends('layouts.default')

@section('title','スタッフ一覧')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endpush
@section('content')
<div class="c-content p-adminStaffLists">
    <div class="c-layout">
        <h1 class="c-title">スタッフ一覧</h1>
        <div class="c-tableBox p-adminStaffLists_content">
            <table class="c-table">
                <tr class="c-table_tr">
                    <th class="c-table_th">名前</th>
                    <th class="c-table_th">メールアドレス</th>
                    <th class="c-table_th">月次勤怠</th>
                </tr>
                @foreach ($users as $user)
                <tr class="c-table_tr">
                    <td class="c-table_td">{{ $user->name }}</td>
                    <td class="c-table_td">{{ $user->email }}</td>
                    <td class="c-table_td"><a href="{{ route('admin.staff_detail',['id' => $user->id]) }}" class="c-table_link">詳細</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
