@extends('admin.layouts.app')

@section('title', 'Thêm Bài Viết Mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Thêm Bài Viết Mới</h3>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-9">
            <div class="card p-4 mb-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu Đề Bài Viết <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg" placeholder="Nhập tiêu đề tại đây..." required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nội Dung Chi Tiết <span class="text-danger">*</span></label>
                    <textarea name="content" id="editor" class="form-control" rows="15"></textarea>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-paper-plane me-1"></i> Đăng Bài
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Trạng Thái</label>
                        <select name="status" class="form-select">
                            <option value="Draft">Bản Nháp</option>
                            <option value="Published">Công Khai</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-save"></i> Lưu Bài Viết</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light fw-bold">
                    <i class="fas fa-image me-1"></i> Ảnh Đại Diện
                </div>
                <div class="card-body text-center">
                    <div class="img-preview mb-2 border bg-light d-flex align-items-center justify-content-center" style="height: 150px; overflow:hidden;">
                        <img id="preview-img" src="https://via.placeholder.com/300x200?text=No+Image" style="max-width: 100%; max-height: 100%;">
                    </div>
                    <input type="file" name="thumbnail_file" class="form-control form-control-sm" onchange="previewImage(this)">
                    <small class="text-muted mt-1 d-block">Dung lượng tối đa 2MB</small>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Nhúng CKEditor (hoặc TinyMCE) --}}
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');

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