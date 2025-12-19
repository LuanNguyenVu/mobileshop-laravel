@extends('layouts.app')
@section('title', 'Xác Thực Email')

@section('content')
<div class="login-container" style="text-align: center;">
    <h2>Xác Thực Email</h2>
    <p>Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng kiểm tra email và bấm vào link xác thực.</p>
    
    @if (session('message'))
        <div class="alert alert-success">Link xác thực mới đã được gửi!</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-login">Gửi Lại Email Xác Thực</button>
    </form>
</div>
@endsection
@push('css') <link rel="stylesheet" href="{{ asset('client/assets/client/css/auth.css') }}"> @endpush