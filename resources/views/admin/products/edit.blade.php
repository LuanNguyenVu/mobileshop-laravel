@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Chỉnh Sửa: {{ $product->product_name }}</h3>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white fw-bold">Thông Tin Cơ Bản</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Tên Sản Phẩm *</label>
                            <input type="text" name="product_name" class="form-control" required value="{{ $product->product_name }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hãng (Brand)</label>
                            <input type="text" name="brand" class="form-control" value="{{ $product->brand }}">
                        </div>
                    </div>
                    
                    <h6 class="text-primary mt-2 mb-3">Thông Số Kỹ Thuật Ngắn</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label class="form-label">Hệ điều hành</label><input type="text" name="operating_system" class="form-control" value="{{ $product->operating_system }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">RAM</label><input type="text" name="ram" class="form-control" value="{{ $product->ram }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">ROM</label><input type="text" name="rom" class="form-control" value="{{ $product->rom }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Pin</label><input type="text" name="battery" class="form-control" value="{{ $product->battery }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">CPU</label><input type="text" name="cpu" class="form-control" value="{{ $product->cpu }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Màn hình</label><input type="text" name="screen" class="form-control" value="{{ $product->screen }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Camera Sau</label><input type="text" name="camera" class="form-control" value="{{ $product->camera }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Camera Trước</label><input type="text" name="front_camera" class="form-control" value="{{ $product->front_camera }}"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white fw-bold">Quản Lý Biến Thể & Giá</div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered mb-0 align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start" width="20%">Màu Sắc *</th>
                                <th width="15%">Ảnh</th>
                                <th width="10%">Tồn Kho</th>
                                <th width="15%">Giá Bán (Gốc) *</th>
                                <th width="15%">Giá Khuyến Mãi</th> <th width="15%">Giá Nhập</th>
                                <th width="5%">#</th>
                            </tr>
                        </thead>
                        <tbody id="variantsBody">
                            @foreach($product->variants as $index => $variant)
                            <tr>
                                <td class="text-start">
                                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                    <input type="text" name="variants[{{ $index }}][color]" class="form-control" value="{{ $variant->color }}" required placeholder="Tên màu">
                                </td>
                                <td>
                                    @if($variant->image)
                                        <img src="{{ asset($variant->image) }}" style="width: 30px; height: 30px; object-fit: cover;" class="mb-1 border rounded">
                                    @endif
                                    <input type="file" name="variants[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                </td>
                                <td><input type="number" name="variants[{{ $index }}][quantity]" class="form-control text-center" value="{{ $variant->quantity }}"></td>
                                <td><input type="number" name="variants[{{ $index }}][selling_price]" class="form-control text-end" value="{{ number_format($variant->selling_price, 0, '', '') }}" required></td>
                                
                                <td><input type="number" name="variants[{{ $index }}][promotional_price]" class="form-control text-end text-danger fw-bold" value="{{ $variant->promotional_price ? number_format($variant->promotional_price, 0, '', '') : '' }}" placeholder="0"></td>
                                
                                <td><input type="number" name="variants[{{ $index }}][purchase_price]" class="form-control text-end text-muted" value="{{ number_format($variant->purchase_price, 0, '', '') }}" placeholder="0"></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantBtn"><i class="fas fa-plus"></i> Thêm Biến Thể Mới</button>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-secondary text-white fw-bold">Thông Số Kỹ Thuật Chi Tiết</div>
                <div class="card-body p-2">
                    <textarea name="detailed_specs" id="specs_editor" class="form-control" rows="10">{!! $product->detailed_specs !!}</textarea>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-dark text-white fw-bold">Bài Viết Mô Tả Sản Phẩm</div>
                <div class="card-body p-2">
                    <textarea name="description" id="desc_editor" class="form-control" rows="10">{!! $product->description !!}</textarea>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header bg-success text-white fw-bold">Cập Nhật</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Loại (Type)</label>
                        <input type="text" name="type" class="form-control" value="{{ $product->type }}">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold">Lưu Thay Đổi</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">Ảnh Đại Diện Chính</div>
                <div class="card-body text-center">
                    <div class="img-preview mb-2 border d-flex align-items-center justify-content-center" style="height: 200px; background: #f8f9fa;">
                        <img id="preview-img" src="{{ asset($product->product_image) }}" onerror="this.src='https://via.placeholder.com/300x300?text=Upload'" style="max-width: 100%; max-height: 100%;">
                    </div>
                    <input type="file" name="product_image_file" class="form-control form-control-sm" onchange="previewImage(this)">
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    // KHỞI TẠO 2 EDITOR
    CKEDITOR.replace('specs_editor');
    CKEDITOR.replace('desc_editor');

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('preview-img').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }

    let variantIndex = {{ count($product->variants) }};
    document.getElementById('addVariantBtn').addEventListener('click', function() {
        const tbody = document.getElementById('variantsBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-start">
                <input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="Màu..." required>
            </td>
            <td><input type="file" name="variants[${variantIndex}][image]" class="form-control form-control-sm" accept="image/*"></td>
            <td><input type="number" name="variants[${variantIndex}][quantity]" class="form-control text-center" value="0"></td>
            <td><input type="number" name="variants[${variantIndex}][selling_price]" class="form-control text-end" required></td>
            
            <td><input type="number" name="variants[${variantIndex}][promotional_price]" class="form-control text-end text-danger fw-bold" placeholder="0"></td>
            
            <td><input type="number" name="variants[${variantIndex}][purchase_price]" class="form-control text-end text-muted" placeholder="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
        `;
        tbody.appendChild(tr);
        variantIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('remove-row') || e.target.parentElement.classList.contains('remove-row'))) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection