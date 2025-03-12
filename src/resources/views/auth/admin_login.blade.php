@extends('layouts.default')

@section('title','管理者ログイン')

@section('content')
<div class="c-authForm">
    <h1 class="c-authForm_title">管理者ログイン</h1>
    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        <input type="hidden" name="role" value="admin">
        <div class="c-authForm_inputGroups">
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="email">メールアドレス</label>
                <input class="c-authForm_input" type="email" id="email" name="email">
            </div>
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="password">パスワード</label>
                <input class="c-authForm_input" type="password" id="password" name="password">
            </div>
            <div class="c-authForm_btns">
                <button class="c-authForm_submitBtn" type="submit">管理者ログインする</button>
            </div>
        </div>
    </form>
</div>
@endsection
