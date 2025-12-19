@extends('admin.layouts.app')

@section('title', 'Quản Lý Tin Tức')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Quản Lý Tin Tức</h3>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Viết Bài Mới</a>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 100px;">Thumbnail</th>
                    <th>Tiêu Đề</th>
                    <th style="width: 150px;">Ngày Tạo</th>
                    <th style="width: 120px;">Trạng Thái</th>
                    <th style="width: 120px;">Tác Vụ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                        <img src="{{ asset($post->thumbnail_path ?? 'assets/images/placeholder.jpg') }}" 
                             alt="Img" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td>
                        <div class="fw-bold text-truncate" style="max-width: 300px;">{{ $post->title }}</div>
                        <small class="text-muted">Slug: {{ $post->slug }}</small>
                    </td>
                    <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($post->status == 'Published')
                            <span class="badge bg-success-light">Published</span>
                        @else
                            <span class="badge bg-warning-light">Draft</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa bài viết này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $posts->links() }}</div>
</div>
@endsection