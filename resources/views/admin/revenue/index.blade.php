@extends('admin.layouts.app')

@section('title', 'Thống Kê Doanh Thu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Thống Kê Doanh Thu</h3>
    
    <form action="{{ route('admin.revenue.index') }}" method="GET" class="d-flex gap-2">
        <select name="month" class="form-select w-auto">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>
            @endfor
        </select>
        <select name="year" class="form-select w-auto">
            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>Năm {{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Xem</button>
    </form>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="stat-icon bg-white text-primary"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-details text-white">
                <h5 class="text-white-50">Đơn Hàng</h5>
                <h2>{{ number_format($summaryStats['orders']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="stat-icon bg-white text-success"><i class="fas fa-box-open"></i></div>
            <div class="stat-details text-white">
                <h5 class="text-white-50">Sản Phẩm Bán</h5>
                <h2>{{ number_format($summaryStats['products_sold']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white">
            <div class="stat-icon bg-white text-info"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-details text-white">
                <h5 class="text-white-50">Doanh Thu</h5>
                <h2>{{ number_format($summaryStats['revenue']) }}đ</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-dark">
            <div class="stat-icon bg-white text-warning"><i class="fas fa-coins"></i></div>
            <div class="stat-details">
                <h5 class="text-muted">Lợi Nhuận (Est)</h5>
                <h2>{{ number_format($summaryStats['profit']) }}đ</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold">Phân Tích Doanh Thu Theo Hãng</div>
            <div class="card-body">
                <canvas id="salesChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-bold">Phân Tích Lợi Nhuận Theo Hãng</div>
            <div class="card-body">
                <canvas id="profitChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Chi Tiết Sản Phẩm Đã Bán (Tháng {{ $month }}/{{ $year }})</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Ngày Xuất</th>
                    <th>Mã Đơn</th>
                    <th>Sản Phẩm</th>
                    <th>Màu Sắc</th>
                    <th>Hãng</th>
                    <th class="text-end">SL</th>
                    <th class="text-end">Doanh Thu</th>
                    <th class="text-end">Lợi Nhuận (Est)</th>
                </tr>
            </thead>
            <tbody>
                @if($soldItems->isEmpty())
                    <tr><td colspan="8" class="text-center text-muted py-3">Không có dữ liệu bán hàng.</td></tr>
                @else
                    @foreach($soldItems as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->order_date)->format('d/m/Y') }}</td>
                        <td><span class="text-primary fw-bold">{{ $item->order_code }}</span></td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->variant_color }}</td>
                        <td>{{ $item->manufacturer }}</td>
                        <td class="text-end fw-bold">{{ $item->quantity }}</td>
                        <td class="text-end text-primary fw-bold">{{ number_format($item->quantity * $item->price_at_order) }}đ</td>
                        <td class="text-end text-success fw-bold">{{ number_format(($item->quantity * $item->price_at_order) * 0.2) }}đ</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end">TỔNG CỘNG:</td>
                    <td class="text-end">{{ number_format($summaryStats['products_sold']) }}</td>
                    <td class="text-end text-primary">{{ number_format($summaryStats['revenue']) }}đ</td>
                    <td class="text-end text-success">{{ number_format($summaryStats['profit']) }}đ</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chuẩn bị dữ liệu từ PHP sang JS
    const brands = @json(array_keys($manufacturerStats));
    const salesData = @json(array_column($manufacturerStats, 'sales'));
    const profitData = @json(array_column($manufacturerStats, 'profit'));

    // 1. Biểu đồ tròn (Sales Chart)
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'doughnut',
        data: {
            labels: brands,
            datasets: [{
                data: salesData,
                backgroundColor: ['#435ebe', '#55c6e8', '#4ecdc4', '#ff6b6b', '#ffe66d'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // 2. Biểu đồ cột (Profit Chart)
    const ctxProfit = document.getElementById('profitChart').getContext('2d');
    new Chart(ctxProfit, {
        type: 'bar',
        data: {
            labels: brands,
            datasets: [{
                label: 'Lợi Nhuận',
                data: profitData,
                backgroundColor: '#198754',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush