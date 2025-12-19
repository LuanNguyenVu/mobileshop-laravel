@extends('admin.layouts.app')

@section('title', 'Quản Lý Đơn Hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Quản Lý Đơn Hàng</h3>
    
    <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
            <option value="Shipped" {{ request('status') == 'Shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Mã đơn, Tên, SĐT..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<div class="card p-3 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Thanh Toán</th>
                    <th>Ngày Đặt</th>
                    <th>Trạng Thái</th>
                    <th>Tác Vụ</th>
                </tr>
            </thead>
            <tbody>
                @if($orders->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không tìm thấy đơn hàng nào.</td>
                    </tr>
                @else
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-primary">
                                {{ $order->order_code }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->receiver_name }}</div>
                            <small class="text-muted">{{ $order->receiver_phone }}</small>
                        </td>
                        <td class="text-danger fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $order->payment_method }}</span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @php
                                $statusClass = match($order->order_status) {
                                    'Pending' => 'bg-warning text-dark',
                                    'Processing' => 'bg-info text-white',
                                    'Shipped' => 'bg-primary',
                                    'Delivered' => 'bg-success',
                                    'Cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $order->order_status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="mt-3 d-flex justify-content-end">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endsection