@extends('layouts.default')

@section('title','会員登録画面')

@section('content')
<div class="c-authMail">
    <p class="c-authMail_text">登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。</p>
    <div class="c-authMail_btns">
        <div class="c-authMail_btn">
            <a href="" class="c-authMail_button">認証はこちらから</a>
        </div>
        <div class="c-authMail_btn"><a href="" class="c-authMail_link">認証メールを再送する</a></div>
    </div>
</div>
@endsection
