@extends('admin.layouts.app')

@section('title', 'Chi Tiết Tài Khoản')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card p-4 text-center">
            <div class="mb-3 d-flex justify-content-center">
                @if($user->avatar_path)
                    <img src="{{ asset($user->avatar_path) }}" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover; padding: 3px;">
                @else
                    <div class="avatar-circle" style="width: 120px; height: 120px; font-size: 40px; margin: 0 auto;">
                        {{ substr($user->username, 0, 1) }}
                    </div>
                @endif
            </div>
            
            <h4 class="fw-bold">{{ $user->username }}</h4>
            <div class="mb-3">
                <span class="badge {{ $user->status == 'Active' ? 'bg-success-light' : 'bg-danger-light' }}">
                    {{ $user->status ?? 'Inactive' }}
                </span>
            </div>

            <div class="text-start mt-4">
                <div class="mb-3">
                    <label class="text-muted small">Email</label>
                    <div class="fw-bold">{{ $user->email }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Số điện thoại</label>
                    <div class="fw-bold">{{ $user->phone ?? 'Chưa cập nhật' }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Ngày tham gia</label>
                    <div class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Địa chỉ</label>
                    <div><i class="fas fa-map-marker-alt text-danger"></i> {{ $user->address ?? 'Chưa cập nhật' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-3">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-orders-tab" data-bs-toggle="pill" data-bs-target="#pills-orders" type="button">
                        <i class="fas fa-shopping-bag me-1"></i> Lịch Sử Đơn Hàng
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-comments-tab" data-bs-toggle="pill" data-bs-target="#pills-comments" type="button">
                        <i class="fas fa-comment-dots me-1"></i> Lịch Sử Bình Luận
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                
                {{-- TAB 1: LỊCH SỬ MUA HÀNG --}}
                <div class="tab-pane fade show active" id="pills-orders">
                    @if($user->orders->isEmpty())
                        <div class="text-center p-4 text-muted">
                            <i class="fas fa-shopping-cart fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p>Tài khoản này chưa có đơn hàng nào.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Ngày Đặt</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                        <th>Chi Tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_code }}</strong></td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                                        <td>
                                            @if($order->order_status == 'Completed')
                                                <span class="badge bg-success-light">Hoàn thành</span>
                                            @elseif($order->order_status == 'Cancelled')
                                                <span class="badge bg-danger-light">Đã hủy</span>
                                            @else
                                                <span class="badge bg-warning-light">{{ $order->order_status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Link sang chi tiết đơn hàng (nếu đã làm trang đó) --}}
                                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- TAB 2: LỊCH SỬ BÌNH LUẬN --}}
                <div class="tab-pane fade" id="pills-comments">
                    {{-- Sử dụng quan hệ reviews từ model User --}}
                    @if($user->reviews->isEmpty())
                        <div class="text-center p-4 text-muted">
                            <i class="far fa-comments fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p>Tài khoản này chưa có bình luận nào.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($user->reviews as $review)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-bold">
                                            {{-- Hiển thị tên sản phẩm mà người dùng đã đánh giá --}}
                                            {{ $review->product->product_name ?? 'Sản phẩm không còn tồn tại' }}
                                        </h6>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    
                                    {{-- Hiển thị số sao --}}
                                    <div class="text-warning small mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>

                                    <p class="mb-1 text-dark" style="font-style: italic;">"{{ $review->comment }}"</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection