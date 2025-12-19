@extends('layouts.app')

@section('title', 'Thông Tin Tài Khoản')
@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/account.css') }}">
@endpush

@section('content')
<div class="container profile-page-container" style="margin-top: 30px; margin-bottom: 30px;">
    
    @if(session('success'))
        <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-info-grid" style="display: flex; gap: 30px;">
        <div class="profile-avatar-section" style="width: 250px; text-align: center;">
            <div class="avatar-placeholder" style="margin-bottom: 15px;">
                <img src="{{ $user->avatar_path ? asset($user->avatar_path) : 'https://via.placeholder.com/150' }}" 
                     alt="Avatar" 
                     style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #eee;">
            </div>
            <h3 style="margin-bottom: 5px;">{{ $user->username }}</h3>
            <p style="color: #666;">Thành viên</p>
        </div>

        <div class="profile-details-section" style="flex: 1; border-left: 1px solid #eee; padding-left: 30px;">
            <h2 class="section-title" style="border-bottom: 2px solid #cc0000; padding-bottom: 10px; margin-bottom: 20px;">Thông Tin Tài Khoản</h2>
            
            <div class="info-line" style="margin-bottom: 15px; display: flex;">
                <span class="info-label" style="font-weight: bold; width: 150px;">Tên Tài Khoản:</span>
                <span class="info-value">{{ $user->username }}</span>
            </div>
            
            {{-- PHẦN HIỂN THỊ TRẠNG THÁI EMAIL --}}
            <div class="info-line" style="margin-bottom: 15px; display: flex; align-items: center;">
                <span class="info-label" style="font-weight: bold; width: 150px;">Email:</span>
                <span class="info-value">
                    {{ $user->email }}
                    
                    @if($user->hasVerifiedEmail())
                        <span style="display: inline-flex; align-items: center; background: #d4edda; color: #155724; font-size: 12px; padding: 2px 8px; border-radius: 10px; margin-left: 10px;">
                            <i class="fas fa-check-circle" style="margin-right: 4px;"></i> Đã xác thực
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; background: #fff3cd; color: #856404; font-size: 12px; padding: 2px 8px; border-radius: 10px; margin-left: 10px;">
                            <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> Chưa xác thực
                        </span>
                    @endif
                </span>
            </div>
            
            <div class="info-line" style="margin-bottom: 15px; display: flex;">
                <span class="info-label" style="font-weight: bold; width: 150px;">Số Điện Thoại:</span>
                <span class="info-value">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
            </div>
            
            <div class="info-line" style="margin-bottom: 15px; display: flex;">
                <span class="info-label" style="font-weight: bold; width: 150px;">Địa Chỉ:</span>
                <span class="info-value">{{ $user->address ?? 'Chưa cập nhật' }}</span>
            </div>

            <div style="margin-top: 30px;">
                <a href="{{ route('account.edit') }}" class="btn-detail" style="text-decoration: none; padding: 10px 20px; background: #007bff; color: white; border-radius: 4px;">
                    Thay Đổi Thông Tin
                </a>
            </div>
        </div>
        
        <div class="support-card" style="width: 250px; border: 1px solid #eee; padding: 15px; text-align: center; height: fit-content;">
            <div class="support-header" style="font-weight: bold; margin-bottom: 10px;">HỖ TRỢ KHÁCH HÀNG</div>
            <img src="{{ asset('client/assets/client/images/support.jpg') }}" alt="Support" style="width: 100%; margin-bottom: 10px;">
            <p style="font-size: 13px;">Gọi ngay để được hỗ trợ</p>
            <a href="tel:18006750" class="support-phone" style="font-size: 20px; color: #cc0000; font-weight: bold; text-decoration: none;">1800 6750</a>
        </div>
    </div>
</div>
@endsection