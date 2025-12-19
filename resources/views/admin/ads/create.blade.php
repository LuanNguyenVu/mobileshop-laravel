@extends('admin.layouts.app')

@section('title', 'Thêm Mới Quảng Cáo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Thêm Mới Quảng Cáo</h3>
    <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <label class="form-label fw-bold">Hình Ảnh Banner</label>
                    <div class="border p-2" style="height: 200px; background: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                        <img id="preview-img" src="https://via.placeholder.com/300x150?text=Preview" style="max-width: 100%; max-height: 100%;">
                    </div>
                    <input type="file" name="image_file" class="form-control mt-2" onchange="previewImage(this)" required>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu Đề</label>
                    <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề quảng cáo..." required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Vị Trí Hiển Thị</label>
                    <select name="display_location" class="form-select">
                        <option value="Trang Chủ">Trang Chủ</option>
                        <option value="Sidebar">Sidebar Tin Tức</option>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ngày Bắt Đầu</label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ngày Kết Thúc</label>
                        <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng Thái</label>
                    <select name="status" class="form-select">
                        <option value="Active">Active (Hiển thị)</option>
                        <option value="Inactive">Inactive (Ẩn)</option>
                    </select>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-save"></i> Lưu Quảng Cáo</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection