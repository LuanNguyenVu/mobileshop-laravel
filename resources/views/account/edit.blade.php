@extends('layouts.app')

@section('title', 'Chỉnh Sửa Thông Tin')
@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/account.css') }}">
@endpush

@section('content')
<div class="container profile-page-container" style="margin-top: 30px; margin-bottom: 30px;">
    
    {{-- Hiển thị thông báo khi gửi lại mail thành công --}}
    @if(session('message'))
        <div class="alert alert-success" style="padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px;">
            <i class="fas fa-paper-plane"></i> {{ session('message') }}
        </div>
    @endif

    <div class="profile-info-grid" style="display: flex; gap: 30px;">
        
        <div class="profile-avatar-section" style="width: 250px; text-align: center;">
            <div class="avatar-placeholder" style="margin-bottom: 15px;">
                <img src="{{ $user->avatar_path ? asset($user->avatar_path) : 'https://via.placeholder.com/150' }}" 
                     alt="Avatar" 
                     style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #eee;">
            </div>

            <form action="{{ route('account.upload_avatar') }}" method="POST" enctype="multipart/form-data" id="avatarUploadForm">
                @csrf
                <div class="form-group-avatar">
                    <label for="avatar_file" class="btn-detail" style="cursor: pointer; padding: 8px 15px; background: #6c757d; color: white; border-radius: 4px; display: inline-block;">
                        <i class="fas fa-camera"></i> Chọn Ảnh Mới
                    </label>
                    <input type="file" name="avatar_file" id="avatar_file" accept="image/*" style="display: none;" onchange="document.getElementById('avatarUploadForm').submit();">
                </div>
                <small class="text-muted" style="display: block; margin-top: 5px; font-size: 12px; color: #999;">Tối đa: 2MB</small>
            </form>
            @error('avatar_file')
                <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="profile-details-section" style="flex: 1; border-left: 1px solid #eee; padding-left: 30px;">
            <h2 class="section-title" style="border-bottom: 2px solid #cc0000; padding-bottom: 10px; margin-bottom: 20px;">Thay Đổi Thông Tin Cá Nhân</h2>
            
            <form action="{{ route('account.update') }}" method="POST">
                @csrf
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Tên Tài Khoản</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('username') <span style="color: red; font-size: 13px;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('email') <span style="color: red; font-size: 13px;">{{ $message }}</span> @enderror

                    {{-- NÚT GỬI LẠI EMAIL KÍCH HOẠT (Chỉ hiện khi chưa Active) --}}
                    @if(!$user->hasVerifiedEmail())
                        <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; color: #856404; font-size: 13px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <i class="fas fa-exclamation-triangle"></i> Email này chưa được xác thực.
                            </div>
                            {{-- Form con để gửi request --}}
                        </div>
                        <div style="text-align: right; margin-top: 5px;">
                            {{-- Lưu ý: Form này phải nằm NGOÀI form update chính, nhưng ở đây ta dùng formaction hoặc JS, 
                                 tuy nhiên để đơn giản và tránh lỗi lồng form, ta dùng button submit đặc biệt --}}
                             {{-- CÁCH TỐT NHẤT: Tạo một form riêng biệt bên dưới hoặc dùng button với form attribute (HTML5) 
                                  nhưng để tương thích tốt nhất, ta dùng một form nhỏ riêng biệt ngay tại đây --}}
                        </div>
                    @endif
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Số Điện Thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Địa Chỉ</label>
                    <textarea name="address" class="form-control" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">{{ old('address', $user->address) }}</textarea>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-detail" style="padding: 10px 20px; background: #cc0000; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        Lưu Thay Đổi
                    </button>
                    <a href="{{ route('account.profile') }}" style="margin-left: 10px; color: #666; text-decoration: none;">Hủy</a>
                </div>
            </form>

            {{-- FORM GỬI LẠI EMAIL (Nằm riêng biệt để không xung đột với form update) --}}
            @if(!$user->hasVerifiedEmail())
                <div style="margin-top: 20px; border-top: 1px dashed #ddd; padding-top: 15px;">
                    <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Bạn chưa nhận được email kích hoạt?</p>
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" style="background: none; border: 1px solid #007bff; color: #007bff; padding: 5px 15px; border-radius: 4px; cursor: pointer;">
                            <i class="fas fa-envelope"></i> Gửi lại email kích hoạt
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection