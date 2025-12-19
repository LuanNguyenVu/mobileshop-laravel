@extends('admin.layouts.app')

@section('title', 'Quản Lý Quảng Cáo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Quản Lý Quảng Cáo</h3>
    <a href="{{ route('admin.ads.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Mới</a>
</div>

<div class="card p-3">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Hình Ảnh</th>
                <th>Tiêu Đề</th>
                <th>Vị Trí</th>
                <th>Thời Gian</th>
                <th>Trạng Thái</th>
                <th>Tác Vụ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ads as $ad)
            <tr>
                <td>{{ $ad->id }}</td>
                <td>
                    <img src="{{ asset($ad->image_path) }}" alt="Ad Img" style="width: 80px; height: 50px; object-fit: cover;">
                </td>
                <td><strong>{{ $ad->title }}</strong></td>
                <td>{{ $ad->display_location }}</td>
                <td>
                    <small>
                        {{ \Carbon\Carbon::parse($ad->start_date)->format('d/m/Y') }} <br>
                        ⬇ <br>
                        {{ \Carbon\Carbon::parse($ad->end_date)->format('d/m/Y') }}
                    </small>
                </td>
                <td>
                    <span class="badge {{ $ad->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $ad->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="mt-3">
        {{ $ads->links() }}
    </div>
</div>
@endsection