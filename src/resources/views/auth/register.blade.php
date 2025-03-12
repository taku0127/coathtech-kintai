@extends('layouts.default')

@section('title','会員登録画面')

@section('content')
<div class="c-authForm">
    <h1 class="c-authForm_title">会員登録</h1>
    <form action="/register" method="POST">
        @csrf
        <div class="c-authForm_inputGroups">
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="name">名前</label>
                <input class="c-authForm_input" type="text" id="name" name="name">
            </div>
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="email">メールアドレス</label>
                <input class="c-authForm_input" type="email" id="email" name="email">
            </div>
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="password">パスワード</label>
                <input class="c-authForm_input" type="password" id="password" name="password">
            </div>
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="password_confirmation">パスワード（確認）</label>
                <input class="c-authForm_input" type="password" id="password_confirmation" name="password_confirmation">
            </div>
            <div class="c-authForm_btns">
                <button class="c-authForm_submitBtn" type="submit">登録する</button>
                <a href="/login" class="c-authForm_link">ログインはこちら</a>
            </div>
        </div>
    </form>
</div>
@endsection
