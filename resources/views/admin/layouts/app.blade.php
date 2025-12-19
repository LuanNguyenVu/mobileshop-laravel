<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - MobileShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --sidebar-width: 260px;
            --header-height: 70px;
            --primary-color: #435ebe; /* Xanh tím hiện đại */
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --bg-body: #f2f7ff;
            --sidebar-bg: #fff;
            --text-color: #25396f;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        /* --- SIDEBAR --- */
        .admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--sidebar-bg);
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: 0.3s;
            overflow-y: auto;
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color);
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 15px;
            margin: 0;
        }

        .sidebar-menu li { margin-bottom: 5px; }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #607080;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
            font-weight: 500;
        }

        .sidebar-menu li a:hover, 
        .sidebar-menu li a.active {
            background: var(--primary-color);
            color: #fff;
            box-shadow: 0 5px 10px rgba(67, 94, 190, 0.3);
        }

        .sidebar-menu i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
        }

        /* --- MAIN CONTENT --- */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }

        .admin-header {
            height: var(--header-height);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .admin-content {
            padding: 30px;
            flex: 1;
        }

        /* --- CARDS & WIDGETS (Dashboard) --- */
        .card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 25px;
            transition: transform 0.3s;
        }

        .stat-card {
            display: flex;
            align-items: center;
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 60px; height: 60px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin-right: 20px;
        }
        
        .stat-icon.blue { background: #e3f2fd; color: #0d6efd; }
        .stat-icon.green { background: #d1e7dd; color: #198754; }
        .stat-icon.orange { background: #ffecb5; color: #ffc107; }
        .stat-icon.red { background: #f8d7da; color: #dc3545; }

        .stat-details h5 { margin: 0; font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-details h2 { margin: 5px 0 0; font-size: 28px; font-weight: 700; color: #333; }

        /* --- TABLES --- */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #444;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #eee;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f1f1f1;
            font-size: 0.95rem;
        }

        .table-hover tbody tr:hover {
            background-color: #fafbfc;
        }

        .table img {
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 60px; height: 60px; object-fit: cover;
        }

        /* --- BADGES (Trạng thái đơn hàng) --- */
        .badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        .bg-success-light { background: #d1e7dd; color: #0f5132; }
        .bg-warning-light { background: #fff3cd; color: #664d03; }
        .bg-danger-light { background: #f8d7da; color: #842029; }
        .bg-info-light { background: #cff4fc; color: #055160; }

        /* --- FORMS --- */
        .form-control, .form-select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(67, 94, 190, 0.15);
            border-color: var(--primary-color);
        }

        .form-label {
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
        }

        /* --- BUTTONS --- */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-primary { background: var(--primary-color); border: none; }
        .btn-primary:hover { background: #344cb7; transform: translateY(-2px); }
        
        .btn-sm i { font-size: 0.8rem; }
        
        /* --- UTILS --- */
        .avatar-circle {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex; align-items: center; justify-content: center;
            color: #555; font-weight: bold;
        }
    </style>
    @stack('css')
</head>
<body>

    <aside class="admin-sidebar">
        <div class="sidebar-header">MOBILE SHOP ADMIN</div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Sản Phẩm
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> Đơn Hàng
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Tài Khoản
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ads.index') }}" class="{{ request()->is('admin/ads*') ? 'active' : '' }}">
                    <i class="fas fa-ad"></i> Quảng Cáo
                </a>
            </li>
            <li>
                <a href="{{ route('admin.posts.index') }}" class="{{ request()->is('admin/posts*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i> Tin Tức
                </a>
            </li>
            <li>
                <a href="{{ route('admin.revenue.index') }}" class="{{ request()->is('admin/revenue*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Thống kê
                </a>
            </li>
        </ul>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <a href="#" class="btn btn-sm btn-light"><i class="fas fa-bars"></i></a>
            <div class="user-menu d-flex align-items-center">
                <span class="me-3">Xin chào, Admin</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="border: none; background: none; cursor: pointer; color: #dc3545; font-weight: 500;">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </header>

        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>

        <footer class="text-center py-3 text-muted border-top bg-white">
            Copyright &copy; 2025 MobileShop Admin.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script nhỏ để toggle active menu nếu cần
    </script>
    @stack('scripts')
</body>
</html>