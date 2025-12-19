@extends('admin.layouts.app')

@section('title', 'Quản Lý Sản Phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Quản Lý Sản Phẩm</h3>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Mới</a>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Mã SP</th>
                    <th>Giá Bán</th>
                    <th>Tồn Kho</th>
                    <th>Trạng Thái</th>
                    <th>Tác Vụ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                @php
                    $minPrice = $product->variants->min('selling_price');
                    $maxPrice = $product->variants->max('selling_price');
                    $totalStock = $product->variants->sum('quantity');
                    $priceDisplay = ($minPrice == $maxPrice) ? number_format($minPrice) : number_format($minPrice) . ' - ' . number_format($maxPrice);
                @endphp
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>
                        <img src="{{ asset($product->product_image ?? 'assets/images/placeholder.jpg') }}" style="width: 50px; height: 50px; object-fit: cover;">
                    </td>
                    <td>
                        <div class="fw-bold">{{ $product->product_name }}</div>
                        <small class="text-muted">{{ $product->manufacturer }}</small>
                    </td>
                    <td>{{ $product->product_code }}</td>
                    <td class="text-danger fw-bold">{{ $priceDisplay }}₫</td>
                    <td>{{ $totalStock }}</td>
                    <td>
                        <span class="badge {{ $product->status == 'in_stock' ? 'bg-success-light' : 'bg-danger-light' }}">
                            {{ $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa sản phẩm này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
</div>
@endsection