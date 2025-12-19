@extends('layouts.app')
@section('title', 'Đăng Nhập')

@section('content')
<div class="login-container">
    <h2>Đăng Nhập</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf 
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>Mật khẩu:</label>
            <input type="password" name="password" required class="form-control">
        </div>

        {{-- THÊM LINK QUÊN MẬT KHẨU TẠI ĐÂY --}}
        <div style="text-align: right; margin-bottom: 15px;">
            <a href="{{ route('password.request') }}" style="font-size: 14px; color: #cc0000;">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn-login">Đăng Nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/auth.css') }}">
@endpush