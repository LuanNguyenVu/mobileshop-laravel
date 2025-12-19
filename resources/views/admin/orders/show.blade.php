@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Chi Tiết Đơn Hàng: <span class="text-primary">#{{ $order->order_code }}</span></h3>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2 text-primary"></i> Danh Sách Sản Phẩm</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Sản Phẩm</th>
                                <th class="text-center">Số Lượng</th>
                                <th class="text-end">Đơn Giá</th>
                                <th class="text-end pe-4">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $item->product_name }}</div>
                                    <small class="text-muted">Màu: {{ $item->variant_color }}</small>
                                </td>
                                <td class="text-center">x{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price_at_order, 0, ',', '.') }}₫</td>
                                <td class="text-end pe-4 fw-bold">{{ number_format($item->price_at_order * $item->quantity, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Tổng tiền hàng:</td>
                                <td class="text-end pe-4 fw-bold">{{ number_format($subtotal, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted">Phí vận chuyển:</td>
                                <td class="text-end pe-4 text-muted">
                                    {{-- Giả định phí ship là hiệu số giữa Tổng thực thu - Tổng tiền hàng --}}
                                    {{ number_format($order->total_amount - $subtotal, 0, ',', '.') }}₫
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-danger fs-5 fw-bold">TỔNG THANH TOÁN:</td>
                                <td class="text-end pe-4 text-danger fs-5 fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Thông Tin Giao Hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Người nhận</label>
                        <div class="fw-bold">{{ $order->receiver_name }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Số điện thoại</label>
                        <div class="fw-bold">{{ $order->receiver_phone }}</div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Địa chỉ</label>
                        <div class="fw-bold">{{ $order->receiver_address }}</div>
                    </div>
                    @if($order->note)
                    <div class="col-md-12">
                        <label class="text-muted small">Ghi chú từ khách hàng</label>
                        <div class="alert alert-warning mb-0">{{ $order->note }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fas fa-cog me-2"></i> Trạng Thái Đơn Hàng
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                        @php
                            $statusColors = [
                                'Pending' => 'warning', 'Processing' => 'info', 
                                'Shipped' => 'primary', 'Delivered' => 'success', 'Cancelled' => 'danger'
                            ];
                            $color = $statusColors[$order->order_status] ?? 'secondary';
                        @endphp
                        <div class="badge bg-{{ $color }} fs-6 mb-2 w-100 py-2">{{ $order->order_status }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cập nhật trạng thái mới:</label>
                        <select name="order_status" class="form-select">
                            <option value="Pending" {{ $order->order_status == 'Pending' ? 'selected' : '' }}>Pending (Chờ xử lý)</option>
                            <option value="Processing" {{ $order->order_status == 'Processing' ? 'selected' : '' }}>Processing (Đang xử lý)</option>
                            <option value="Shipped" {{ $order->order_status == 'Shipped' ? 'selected' : '' }}>Shipped (Đang giao hàng)</option>
                            <option value="Delivered" {{ $order->order_status == 'Delivered' ? 'selected' : '' }}>Delivered (Đã giao hàng)</option>
                            <option value="Cancelled" {{ $order->order_status == 'Cancelled' ? 'selected' : '' }}>Cancelled (Hủy đơn)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">Cập Nhật Ngay</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">Thông Tin Khác</div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="text-muted">Ngày đặt hàng:</span><br>
                    <strong>{{ $order->created_at->format('d/m/Y H:i:s') }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Phương thức thanh toán:</span><br>
                    <strong>{{ $order->payment_method }}</strong>
                </div>
                <div>
                    <span class="text-muted">Tài khoản đặt:</span><br>
                    @if($order->user)
                        <a href="{{ route('admin.users.show', $order->user_id) }}">{{ $order->user->username ?? 'User #'.$order->user_id }}</a>
                    @else
                        <span class="text-muted fst-italic">Khách vãng lai</span>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection