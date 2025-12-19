@extends('admin.layouts.app')

@section('title', 'Quản Lý Tài Khoản')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Quản Lý Tài Khoản</h3>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Thành Viên</th>
                    <th>Email</th>
                    <th>Số Điện Thoại</th>
                    <th>Ngày Tham Gia</th>
                    <th>Trạng Thái</th>
                    <th>Tác Vụ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($user->avatar_path)
                                <img src="{{ asset($user->avatar_path) }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="avatar-circle me-2" style="width: 40px; height: 40px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                    {{ substr($user->username, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold">{{ $user->username }}</div>
                                <small class="text-muted">User</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '---' }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    
                    {{-- SỬA PHẦN TRẠNG THÁI DỰA THEO EMAIL_VERIFIED_AT --}}
                    <td>
                        @if($user->email_verified_at)
                            {{-- Nếu có ngày kích hoạt -> Active --}}
                            <span class="badge bg-success" style="padding: 8px 12px;">Active</span>
                        @else
                            {{-- Nếu NULL -> Inactive --}}
                            <span class="badge bg-secondary" style="padding: 8px 12px;">Inactive</span>
                        @endif
                    </td>

                    <td>
                        {{-- Nút Xem Chi Tiết --}}
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Nút Xóa: Chỉ cho phép xóa nếu chưa kích hoạt (Inactive) --}}
                        @if(!$user->email_verified_at)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản chưa kích hoạt này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa tài khoản">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @else
                            {{-- Nếu đã Active thì khóa nút xóa để an toàn --}}
                            <button class="btn btn-sm btn-secondary" disabled title="Không thể xóa tài khoản đang hoạt động">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection