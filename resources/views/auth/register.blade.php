@extends('layouts.app')
@section('title', 'Đăng Ký')

@section('content')
<div class="register-container">
    <h2>Đăng Ký Tài Khoản</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div class="form-group">
            <label>Số điện thoại:</label>
            <input type="text" name="phone" required class="form-control">
        </div>
        <div class="form-group">
            <label>Mật khẩu:</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu:</label>
            <input type="password" name="password_confirmation" required class="form-control">
        </div>

        <button type="submit" class="btn-register">Đăng Ký</button>
    </form>
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/auth.css') }}">
@endpush