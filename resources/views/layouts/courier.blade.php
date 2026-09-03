<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q8K4Z88BFE"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-Q8K4Z88BFE');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Courier') - LUMINA</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --accent: #f97316;
            --accent-light: #ffedd5;
            --dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
            --white: #ffffff;
            --off-white: #fafafa;
            --border-color: #e5e7eb;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --transition: all 0.2s ease;
            --sidebar-width: 260px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            background-color: var(--off-white);
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Sidebar - Clean & Minimal */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--white);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            z-index: 1000;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .sidebar-brand-text {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.5px;
        }
        
        .sidebar-brand-text span {
            font-weight: 400;
            color: var(--gray);
            font-size: 0.75rem;
            display: block;
            letter-spacing: 0;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
            flex: 1;
        }
        
        .sidebar-label {
            padding: 0.75rem 1.5rem 0.5rem;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray);
        }
        
        .sidebar-menu .nav-link {
            color: var(--gray);
            padding: 0.625rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
            font-size: 0.875rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            margin: 2px 0;
        }
        
        .sidebar-menu .nav-link:hover {
            color: var(--dark);
            background: var(--gray-light);
        }
        
        .sidebar-menu .nav-link.active {
            color: var(--primary);
            background: var(--primary-light);
            border-left-color: var(--primary);
            font-weight: 600;
        }
        
        .sidebar-menu .nav-link i {
            width: 18px;
            font-size: 0.9375rem;
            text-align: center;
        }
        
        .sidebar-menu .nav-link .badge {
            margin-left: auto;
            font-size: 0.6875rem;
            padding: 0.25rem 0.5rem;
            font-weight: 600;
        }
        
        .sidebar-divider {
            height: 1px;
            background: var(--border-color);
            margin: 0.75rem 1.5rem;
        }
        
        /* User Panel */
        .user-panel {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-panel-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .user-panel-info {
            flex: 1;
        }
        
        .user-panel-info h6 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .user-panel-info span {
            font-size: 0.75rem;
            color: var(--gray);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: var(--white);
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .top-navbar .page-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .top-navbar .page-breadcrumb {
            font-size: 0.8125rem;
            color: var(--gray);
        }
        
        .top-navbar .page-breadcrumb a {
            color: var(--gray);
            text-decoration: none;
        }
        
        .top-navbar .page-breadcrumb a:hover {
            color: var(--primary);
        }
        
        /* Notification Dropdown */
        .notification-btn {
            position: relative;
            background: var(--gray-light);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
            transition: var(--transition);
        }
        
        .notification-btn:hover {
            background: var(--border-color);
            color: var(--dark);
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #dc2626;
            color: white;
            font-size: 0.625rem;
            font-weight: 600;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        /* Page Content Container */
        .page-content {
            padding: 1.5rem;
        }
        
        /* Cards - Clean Style */
        .card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: none;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--dark);
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Stat Cards - Minimal */
        .stat-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .stat-icon.primary {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .stat-icon.accent {
            background: var(--accent-light);
            color: var(--accent);
        }
        
        .stat-icon.success {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .stat-icon.purple {
            background: #f3e8ff;
            color: #9333ea;
        }
        
        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.125rem;
            line-height: 1;
        }
        
        .stat-info p {
            font-size: 0.8125rem;
            color: var(--gray);
            margin: 0;
        }
        
        /* Delivery Card */
        .delivery-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--primary);
            border-radius: var(--radius);
            padding: 1.25rem;
            transition: var(--transition);
            margin-bottom: 1rem;
        }
        
        .delivery-card:hover {
            box-shadow: var(--shadow-md);
        }
        
        /* Buttons */
        .btn {
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-outline-primary {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn-light {
            background: var(--gray-light);
            border: 1px solid var(--border-color);
            color: var(--dark);
        }
        
        .btn-light:hover {
            background: var(--border-color);
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }
        
        /* Tables - Clean */
        .table {
            margin: 0;
        }
        
        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray);
            background: var(--gray-light);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
        }
        
        .table td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--dark);
        }
        
        .table tbody tr:hover {
            background: var(--off-white);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Badges - Minimal */
        .badge {
            font-weight: 600;
            font-size: 0.6875rem;
            padding: 0.375rem 0.625rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge.bg-success {
            background: #dcfce7 !important;
            color: #15803d;
        }
        
        .badge.bg-warning {
            background: var(--accent-light) !important;
            color: #c2410c;
        }
        
        .badge.bg-danger {
            background: #fee2e2 !important;
            color: #dc2626;
        }
        
        .badge.bg-info {
            background: var(--primary-light) !important;
            color: var(--primary);
        }
        
        .badge.bg-secondary {
            background: var(--gray-light) !important;
            color: var(--gray);
        }
        
        .badge.bg-primary {
            background: var(--primary-light) !important;
            color: var(--primary);
        }
        
        /* Forms */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        
        .form-label {
            font-weight: 600;
            font-size: 0.8125rem;
            color: var(--dark);
            margin-bottom: 0.375rem;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #15803d;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .alert-warning {
            background: var(--accent-light);
            color: #c2410c;
        }
        
        .alert-info {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: var(--dark);
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background: var(--gray-light);
        }
        
        .dropdown-item i {
            width: 18px;
            margin-right: 0.5rem;
            color: var(--gray);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }
        
        .empty-state h5 {
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--gray);
            font-size: 0.875rem;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-light);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray);
        }
        
        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-backdrop.show {
                display: block;
            }
            
            /* Hide sidebar completely on mobile, use bottom nav instead */
            .sidebar {
                display: none;
            }
            
            .sidebar-toggle {
                display: none !important;
            }
            
            /* Fixed top navbar on mobile */
            .top-navbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1040;
            }
            
            .main-content {
                padding-top: 60px;
            }
            
            body {
                padding-bottom: 70px;
            }
        }
        
        /* Toggle Button */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            padding: 0.5rem;
            color: var(--dark);
            font-size: 1.25rem;
        }
        
        @media (min-width: 992px) {
            .sidebar-toggle {
                display: none;
            }
        }
        
        /* Mobile Bottom Navigation */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--white);
            border-top: 1px solid var(--border-color);
            padding: 0.5rem 0;
            padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));
            z-index: 1050;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .mobile-bottom-nav-inner {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--gray);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            transition: var(--transition);
            position: relative;
            min-width: 60px;
        }
        
        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: var(--primary);
        }
        
        .mobile-nav-item.active {
            background: var(--primary-light);
        }
        
        .mobile-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 0.125rem;
        }
        
        .mobile-nav-item span {
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .mobile-nav-badge {
            position: absolute;
            top: -2px;
            right: 8px;
            background: #dc2626;
            color: white;
            font-size: 0.55rem;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
        
        @media (max-width: 991.98px) {
            .mobile-bottom-nav {
                display: block;
            }
        }

        [data-auto-parallax] {
            --np-parallax-shift: 0px;
            transform: translate3d(0, var(--np-parallax-shift), 0);
            will-change: transform;
            transition: transform 420ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        @media (prefers-reduced-motion: reduce) {
            [data-auto-parallax] {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar Backdrop (Mobile) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset(config('branding.logo', 'storage/logo.png')) }}" alt="{{ config('branding.name', 'LUMINA') }}" height="40" class="brand-logo">
            <div class="sidebar-brand-text">
                {{ config('branding.name', 'LUMINA') }}
                <span>Courier Panel</span>
            </div>
        </div>
        
        <nav class="sidebar-menu">
            <div class="sidebar-label">Main Menu</div>
            
            <a href="{{ route('courier.dashboard') }}" class="nav-link {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            
            <a href="{{ route('courier.deliveries.index') }}" class="nav-link {{ request()->routeIs('courier.deliveries.index') || request()->routeIs('courier.deliveries.show') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Delivery Tasks
                @php
                    $activeDeliveries = auth()->user()->activeDeliveries()->count();
                @endphp
                @if($activeDeliveries > 0)
                    <span class="badge bg-danger">{{ $activeDeliveries }}</span>
                @endif
            </a>
            
            <a href="{{ route('courier.deliveries.history') }}" class="nav-link {{ request()->routeIs('courier.deliveries.history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> History
            </a>
            
            <div class="sidebar-divider"></div>
            <div class="sidebar-label">Account</div>
            
            <a href="{{ route('courier.profile') }}" class="nav-link {{ request()->routeIs('courier.profile') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i> My Profile
            </a>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" style="color: var(--gray);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
        
        <!-- User Panel -->
        <div class="user-panel">
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                 class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            <div class="user-panel-info">
                <h6>{{ Str::limit(auth()->user()->name, 15) }}</h6>
                <span>Active Courier</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                        <div class="page-breadcrumb">
                            @yield('breadcrumb')
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="notification-btn" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <div class="px-3 py-2 border-bottom">
                            <h6 class="mb-0 fw-600">Notifikasi</h6>
                        </div>
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <a class="dropdown-item py-2" href="{{ route('courier.deliveries.show', $notification->data['order_id'] ?? 0) }}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="stat-icon primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-600 small">{{ $notification->data['title'] ?? 'Notifikasi Baru' }}</div>
                                        <small class="text-muted">{{ Str::limit($notification->data['message'] ?? '', 50) }}</small>
                                        <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-bell-slash mb-2"></i>
                                <p class="mb-0 small">Tidak ada notifikasi baru</p>
                            </div>
                        @endforelse
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <div class="border-top px-3 py-2">
                                <form action="{{ route('courier.notifications.markRead') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        Tandai Semua Dibaca
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <div class="mobile-bottom-nav-inner">
            <a href="{{ route('courier.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <br>
            </a>
            <a href="{{ route('courier.deliveries.index') }}" class="mobile-nav-item {{ request()->routeIs('courier.deliveries.index') || request()->routeIs('courier.deliveries.show') ? 'active' : '' }}">
                <i class="fas fa-truck"></i>
                <br>
                @php
                    $activeDeliveries = auth()->user()->activeDeliveries()->count();
                @endphp
                @if($activeDeliveries > 0)
                    <span class="mobile-nav-badge">{{ $activeDeliveries }}</span>
                @endif
            </a>
            <a href="{{ route('courier.deliveries.history') }}" class="mobile-nav-item {{ request()->routeIs('courier.deliveries.history') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <br>
            </a>
            <a href="{{ route('courier.profile') }}" class="mobile-nav-item {{ request()->routeIs('courier.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <br>
            </a>
        </div>
    </nav>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle for Mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            });
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }
    </script>

    <script>
        (function () {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReducedMotion) return;

            const selector = [
                '.page-content > .row',
                '.page-content > .container',
                '.page-content > .container-fluid',
                '.page-content > .card',
                '.page-content > form',
                '.page-content > section',
                '.page-content > table'
            ].join(', ');

            const candidates = Array.from(document.querySelectorAll(selector)).filter((el) => {
                if (el.hasAttribute('data-auto-parallax') || el.hasAttribute('data-parallax')) return false;
                if (!el.offsetParent || el.offsetHeight < 48) return false;

                const style = window.getComputedStyle(el);
                if (style.position === 'fixed' || style.position === 'sticky') return false;
                if (el.closest('.sidebar, .top-navbar, .mobile-bottom-nav')) return false;

                return true;
            });

            if (!candidates.length) return;

            candidates.forEach((el) => {
                const speed = el.matches('.page-content > .row, .page-content > .container, .page-content > .container-fluid') ? 0.013 : 0.01;
                el.dataset.autoParallax = '1';
                el.dataset.autoParallaxSpeed = String(speed);
            });

            let ticking = false;

            const updateParallax = () => {
                const viewportH = window.innerHeight || document.documentElement.clientHeight;

                candidates.forEach((el) => {
                    const speed = Number.parseFloat(el.dataset.autoParallaxSpeed || '0.01') || 0.01;
                    const rect = el.getBoundingClientRect();
                    const centerY = rect.top + (rect.height / 2);
                    const offsetFromCenter = centerY - (viewportH / 2);
                    const rawShift = -offsetFromCenter * speed;
                    const maxShift = 10;
                    const shift = Math.max(-maxShift, Math.min(maxShift, rawShift));
                    el.style.setProperty('--np-parallax-shift', `${shift.toFixed(2)}px`);
                });

                ticking = false;
            };

            const requestTick = () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            };

            window.addEventListener('scroll', requestTick, { passive: true });
            window.addEventListener('resize', requestTick);
            requestTick();
        })();
    </script>
    
    {{-- Notification Sound Component --}}
    <x-notification-sound role="courier" />
    
    @stack('scripts')
</body>
</html>
