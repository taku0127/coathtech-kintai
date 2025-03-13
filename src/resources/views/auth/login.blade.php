@extends('layouts.default')

@section('title','ログイン画面')

@section('content')
<div class="c-authForm">
    <h1 class="c-authForm_title">ログイン</h1>
    <form action="/login" method="POST">
        @csrf
        <div class="c-authForm_inputGroups">
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="email">メールアドレス</label>
                <input class="c-authForm_input" type="email" id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <p class="c-authForm_error">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="c-authForm_inputGroup">
                <label class="c-authForm_label" for="password">パスワード</label>
                <input class="c-authForm_input" type="password" id="password" name="password">
                @error('password')
                    <p class="c-authForm_error">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="c-authForm_btns">
                <button class="c-authForm_submitBtn" type="submit">ログインする</button>
                <a href="/register" class="c-authForm_link">会員登録はこちら</a>
            </div>
        </div>
    </form>
</div>
@endsection
