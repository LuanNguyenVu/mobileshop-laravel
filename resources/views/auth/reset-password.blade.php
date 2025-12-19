@extends('layouts.app')
@section('title', 'Đặt Lại Mật Khẩu')

@section('content')
<div class="login-container">
    <h2>Đặt Lại Mật Khẩu Mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div class="form-group">
            <label>Mật khẩu mới:</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu:</label>
            <input type="password" name="password_confirmation" required class="form-control">
        </div>

        <button type="submit" class="btn-login">Đổi Mật Khẩu</button>
    </form>
</div>
@endsection
@push('css') <link rel="stylesheet" href="{{ asset('client/assets/client/css/auth.css') }}"> @endpush