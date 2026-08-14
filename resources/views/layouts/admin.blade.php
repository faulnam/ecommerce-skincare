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
    <title>@yield('title', 'Admin - Hijab')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0f172a;
            --primary-dark: #020617;
            --primary-light: #e2e8f0;
            --accent: #34d399;
            --accent-light: #d1fae5;
            --dark: #0f172a;
            --gray: #64748b;
            --gray-light: #f1f5f9;
            --white: #ffffff;
            --off-white: #f8fafc;
            --border-color: #e2e8f0;
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
            font-family: 'Inter', sans-serif;
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
        
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            background: var(--gray-light);
            color: var(--dark);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .user-dropdown .dropdown-toggle:hover {
            background: var(--border-color);
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8125rem;
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
        
        .stat-icon.info {
            background: #dbeafe;
            color: #0f172a;
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
        
        .stat-change {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .stat-change.up {
            color: var(--primary);
        }
        
        .stat-change.down {
            color: #dc2626;
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
            background: var(--primary-light) !important;
            color: var(--primary-dark);
        }
        
        .badge.bg-warning {
            background: var(--accent-light) !important;
            color: #065f46;
        }
        
        .badge.bg-danger {
            background: #fee2e2 !important;
            color: #dc2626;
        }
        
        .badge.bg-info {
            background: #e2e8f0 !important;
            color: #334155;
        }
        
        .badge.bg-secondary {
            background: var(--gray-light) !important;
            color: var(--gray);
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
            background: var(--primary-light);
            color: var(--primary-dark);
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .alert-warning {
            background: var(--accent-light);
            color: #065f46;
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
        
        /* Pagination */
        .pagination {
            gap: 0.25rem;
        }
        
        .page-link {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm) !important;
            color: var(--dark);
            font-size: 0.875rem;
            padding: 0.5rem 0.875rem;
        }
        
        .page-link:hover {
            background: var(--gray-light);
            border-color: var(--border-color);
            color: var(--dark);
        }
        
        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--gray-light);
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
        
        @media (max-width: 991.98px) {
            .sidebar-toggle {
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
            <img src="{{ asset(config('branding.logo', 'storage/logo.png')) }}" alt="{{ config('branding.name', 'Hijab') }}" height="40" class="brand-logo">
            <div class="sidebar-brand-text">
                {{ config('branding.name', 'Hijab') }}
                <span>Admin Panel</span>
            </div>
        </div>
        
        <nav class="sidebar-menu">
            @if(!auth()->user()->isDeveloper() && !auth()->user()->isBlogger())
            <div class="sidebar-label">Main Menu</div>
            @endif
            
            @if(!auth()->user()->isDeveloper() && !auth()->user()->isBlogger())
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            

            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Products
            </a>
            
            <a href="{{ route('admin.specifications.index') }}" class="nav-link {{ request()->routeIs('admin.specifications.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Spesifikasi Produk
            </a>
            <a href="{{ route('admin.filters.index') }}" class="nav-link {{ request()->routeIs('admin.filters.*') ? 'active' : '' }}">
                <i class="fas fa-filter"></i> Kelola Filter
            </a>

            <a href="{{ route('admin.brand-catalogs.index') }}" class="nav-link {{ request()->routeIs('admin.brand-catalogs.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Brand Catalogs
            </a>

            <a href="{{ route('admin.store-information.index') }}" class="nav-link {{ request()->routeIs('admin.store-information.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i> Informasi Toko
            </a>
            @endif


            @if(!auth()->user()->isDeveloper())
            <a href="{{ route('admin.insights.index') }}" class="nav-link {{ request()->routeIs('admin.insights.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Insights
            </a>
            @endif

            @if(!auth()->user()->isDeveloper() && !auth()->user()->isBlogger())
            <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Testimonials & Reviews
                @php
                    $pendingTestimonials = \App\Models\Testimonial::where('is_approved', false)->count();
                @endphp
                @if($pendingTestimonials > 0)
                    <span class="badge bg-warning">{{ $pendingTestimonials }}</span>
                @endif
            </a>
            @endif
            
            @if(!auth()->user()->isBlogger())
            <div class="sidebar-divider"></div>
            <div class="sidebar-label">Manage</div>
            @endif
            
            @if(auth()->user()->isDeveloper())
            <a href="{{ route('admin.notification-logs.index') }}" class="nav-link {{ request()->routeIs('admin.notification-logs.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Log Email Notifikasi
            </a>

            <a href="{{ route('admin.biteship-logs.index') }}" class="nav-link {{ request()->routeIs('admin.biteship-logs.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Log Biteship
            </a>

            <a href="{{ route('admin.paylabs-logs.index') }}" class="nav-link {{ request()->routeIs('admin.paylabs-logs.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Log Paylabs
            </a>
            @endif
            
            @if(!auth()->user()->isDeveloper() && !auth()->user()->isBlogger())
            <a href="{{ route('admin.chats.index') }}" class="nav-link {{ request()->routeIs('admin.chats.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Live Chat
                <span class="badge bg-danger" id="live-chat-badge" style="display: none; margin-left: auto;">0</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Customers
            </a>
            
            
            <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Staff
            </a>
        
            <a href="{{ route('admin.shipping-discounts.index') }}" class="nav-link {{ request()->routeIs('admin.shipping-discounts.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Shipping Discounts
            </a>
            
            <a href="{{ route('admin.vouchers.index') }}" class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> Vouchers
            </a>
            
            <a href="{{ route('admin.history.index') }}" class="nav-link {{ request()->routeIs('admin.history.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i> History
            </a>
            
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-download"></i> Reports
            </a>
            
            <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i> Notifications
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            @endif
            
            <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i> My Profile
            </a>
            
            <div class="sidebar-divider"></div>
            
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Website
            </a>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" style="color: var(--gray);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
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
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                        <div class="page-breadcrumb">
                            @yield('breadcrumb')
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="user-dropdown dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                         class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                    <span class="d-none d-md-inline">{{ Str::limit(auth()->user()->name, 15) }}</span>
                    <i class="fas fa-chevron-down fa-xs ms-1"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                            <i class="fas fa-user-cog"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                            <i class="fas fa-globe"></i> Lihat Website
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
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

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
    <x-notification-sound role="admin" />
    
    <script>
        (function() {
            let lastLiveChatCount = 0;
            function checkLiveChats() {
                fetch('{{ route("admin.chats.count") }}')
                    .then(res => res.json())
                    .then(data => {
                        const badge = document.getElementById('live-chat-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                        
                        if (data.count > lastLiveChatCount && typeof window.notificationManager !== 'undefined') {
                            window.notificationManager.showToast({
                                title: 'Live Chat Baru!',
                                message: 'Ada pelanggan yang meminta bantuan admin.',
                                type: 'default',
                                url: '{{ route("admin.chats.index") }}'
                            });
                            window.notificationManager.playSound('default');
                        }
                        lastLiveChatCount = data.count;
                    })
                    .catch(err => console.error(err));
            }
            setInterval(checkLiveChats, 5000);
            checkLiveChats();
        })();
    </script>

    {{-- Auto-compresses images picked in file inputs before the form submits --}}
    <script src="{{ asset('js/image-compress.js') }}"></script>

    @stack('scripts')
</body>
</html>
