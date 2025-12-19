@extends('layouts.app')
@section('title', 'Quên Mật Khẩu')

@section('content')
<div class="login-container">
    <h2>Quên Mật Khẩu</h2>
    <p style="font-size: 14px; margin-bottom: 20px;">Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <button type="submit" class="btn-login">Gửi Link Reset</button>
    </form>
</div>
@endsection
@push('css') <link rel="stylesheet" href="{{ asset('client/assets/client/css/auth.css') }}"> @endpush