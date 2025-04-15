@extends('layouts.default')

@section('title','会員登録画面')

@section('content')
<div class="c-authMail">
    <p class="c-authMail_text">登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。</p>
    <div class="c-authMail_btns">
        <div class="c-authMail_btn">
            <a href="/email/verify" class="c-authMail_button">認証はこちらから</a>
        </div>
        <form action="/email/verification-notification" method="POST">
            @csrf
            <div class="c-authMail_btn"><button class="c-authMail_link">認証メールを再送する</button></div>
        </form>
    </div>
</div>
@endsection
