@extends('admin.layouts.app')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Thêm Sản Phẩm Mới</h3>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    <div class="row">
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white fw-bold">Thông Tin Cơ Bản</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Tên Sản Phẩm *</label>
                            <input type="text" name="product_name" class="form-control" required placeholder="Ví dụ: iPhone 15 Pro Max">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hãng (Brand)</label>
                            <input type="text" name="brand" class="form-control" placeholder="Apple, Samsung...">
                        </div>
                    </div>
                    
                    <h6 class="text-primary mt-2 mb-3">Thông Số Kỹ Thuật Ngắn</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label class="form-label">Hệ điều hành</label><input type="text" name="operating_system" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">RAM</label><input type="text" name="ram" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">ROM</label><input type="text" name="rom" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Pin</label><input type="text" name="battery" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">CPU</label><input type="text" name="cpu" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Màn hình</label><input type="text" name="screen" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Camera Sau</label><input type="text" name="camera" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Camera Trước</label><input type="text" name="front_camera" class="form-control"></div>
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
                                <th width="15%">Giá Khuyến Mãi</th>
                                <th width="15%">Giá Nhập</th>
                                <th width="5%">#</th>
                            </tr>
                        </thead>
                        <tbody id="variantsBody">
                            <tr>
                                <td class="text-start">
                                    <input type="text" name="variants[0][color]" class="form-control" placeholder="Màu..." required>
                                </td>
                                <td>
                                    <input type="file" name="variants[0][image]" class="form-control form-control-sm" accept="image/*">
                                </td>
                                <td><input type="number" name="variants[0][quantity]" class="form-control text-center" value="0"></td>
                                <td><input type="number" name="variants[0][selling_price]" class="form-control text-end" required placeholder="0"></td>
                                <td><input type="number" name="variants[0][promotional_price]" class="form-control text-end text-danger fw-bold" placeholder="0"></td>
                                <td><input type="number" name="variants[0][purchase_price]" class="form-control text-end text-muted" placeholder="0"></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="p-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantBtn"><i class="fas fa-plus"></i> Thêm Biến Thể Mới</button>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0" id="productContentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-3 px-4 rounded-0 border-top-0 border-start-0" 
                                    id="specs-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#specs-pane" 
                                    type="button" role="tab" 
                                    aria-controls="specs-pane" 
                                    aria-selected="true"
                                    style="color: #435ebe;">
                                <i class="fas fa-list-alt me-2"></i> Thông Số Kỹ Thuật
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-3 px-4 rounded-0 border-top-0" 
                                    id="desc-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#desc-pane" 
                                    type="button" role="tab" 
                                    aria-controls="desc-pane" 
                                    aria-selected="false"
                                    style="color: #25396f;">
                                <i class="fas fa-pen-nib me-2"></i> Bài Viết Mô Tả
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-3">
                    <div class="tab-content" id="productContentTabContent">
                        
                        <div class="tab-pane fade show active" id="specs-pane" role="tabpanel" aria-labelledby="specs-tab">
                            <div class="mb-2 text-muted small"><i class="fas fa-info-circle"></i> Nhập bảng cấu hình chi tiết (Màn hình, CPU, Pin...)</div>
                            <textarea name="detailed_specs" id="specs_editor" class="form-control" rows="15">
                                {{-- Nếu là trang Edit thì hiển thị dữ liệu cũ, trang Create để trống --}}
                                @if(isset($product)) {!! $product->detailed_specs !!} @endif
                            </textarea>
                        </div>

                        <div class="tab-pane fade" id="desc-pane" role="tabpanel" aria-labelledby="desc-tab">
                            <div class="mb-2 text-muted small"><i class="fas fa-info-circle"></i> Nhập bài viết đánh giá chi tiết sản phẩm, hình ảnh thực tế...</div>
                            <textarea name="description" id="desc_editor" class="form-control" rows="15">
                                {{-- Nếu là trang Edit thì hiển thị dữ liệu cũ, trang Create để trống --}}
                                @if(isset($product)) {!! $product->description !!} @endif
                            </textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header bg-success text-white fw-bold">Đăng Sản Phẩm</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Loại (Type)</label>
                        <input type="text" name="type" class="form-control" placeholder="flagship, mid-range...">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold">Lưu Sản Phẩm</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">Ảnh Đại Diện Chính</div>
                <div class="card-body text-center">
                    <div class="img-preview mb-2 border d-flex align-items-center justify-content-center" style="height: 200px; background: #f8f9fa;">
                        <img id="preview-img" src="https://via.placeholder.com/300x300?text=Upload" style="max-width: 100%; max-height: 100%;">
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

    let variantIndex = 1;
    document.getElementById('addVariantBtn').addEventListener('click', function() {
        const tbody = document.getElementById('variantsBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-start">
                <input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="Màu..." required>
            </td>
            <td><input type="file" name="variants[${variantIndex}][image]" class="form-control form-control-sm" accept="image/*"></td>
            <td><input type="number" name="variants[${variantIndex}][quantity]" class="form-control text-center" value="0"></td>
            <td><input type="number" name="variants[${variantIndex}][selling_price]" class="form-control text-end" required placeholder="0"></td>
            
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