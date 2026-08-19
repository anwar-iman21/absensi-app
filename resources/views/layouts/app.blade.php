<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi Digital') — {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* SIDEBAR */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(160deg, #1e293b 0%, #0f172a 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            transition: transform .3s ease;
            overflow-y: auto;
        }
        #sidebar .brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        #sidebar .brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1.1rem; }
        #sidebar .brand small { color: #94a3b8; font-size: .75rem; }
        #sidebar .nav-section {
            padding: .75rem 1rem .25rem;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 600;
        }
        #sidebar .nav-link {
            color: #cbd5e1;
            padding: .6rem 1.25rem;
            border-radius: .5rem;
            margin: .1rem .75rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .875rem;
            transition: all .2s;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: rgba(79,70,229,.25);
            color: #fff;
        }
        #sidebar .nav-link i { font-size: 1rem; width: 1.2rem; text-align: center; }

        /* MAIN CONTENT */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin .3s ease;
        }
        #topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        /* CARDS */
        .stat-card {
            border: none;
            border-radius: 1rem;
            padding: 1.25rem;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
        .stat-card .icon-box {
            width: 52px; height: 52px;
            border-radius: .75rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .stat-card .stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: .8rem; color: #64748b; margin-top: .25rem; }

        /* TABLE */
        .table-modern { border-collapse: separate; border-spacing: 0; }
        .table-modern thead th {
            background: #f8fafc;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: .75rem 1rem;
        }
        .table-modern tbody td { padding: .75rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .table-modern tbody tr:hover td { background: #f8fafc; }

        /* BADGE */
        .badge { font-size: .75rem; padding: .35em .65em; }

        /* CARD PAGE */
        .page-card { background: #fff; border-radius: 1rem; border: none; box-shadow: 0 1px 3px rgba(0,0,0,.07); }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }

        /* Dark mode */
        [data-bs-theme="dark"] body { background: #0f172a; }
        [data-bs-theme="dark"] #topbar { background: #1e293b; border-color: #334155; }
        [data-bs-theme="dark"] .page-card { background: #1e293b; }
        [data-bs-theme="dark"] .table-modern thead th { background: #1e293b; }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:38px;height:38px;background:var(--primary);border-radius:.6rem;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-fingerprint text-white fs-5"></i>
            </div>
            <div>
                <h5>AbsensiApp</h5>
                <small>{{ auth()->user()->role == 'admin' ? 'Administrator' : 'Karyawan Portal' }}</small>
            </div>
        </div>
    </div>

    <div class="py-2">
        @if(auth()->user()->isAdmin())
            <div class="nav-section">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('admin.attendance.scan') }}" class="nav-link {{ request()->routeIs('admin.attendance.scan') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> Scan Absensi
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i> Data Absensi
            </a>

            <div class="nav-section">Karyawan</div>
            <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Data Karyawan
            </a>
            <a href="{{ route('admin.shifts.index') }}" class="nav-link {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}">
                <i class="bi bi-clock-fill"></i> Shift Kerja
            </a>

            <div class="nav-section">Penggajian</div>
            <a href="{{ route('admin.payroll.index') }}" class="nav-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Payroll / Gaji
            </a>
            <a href="{{ route('admin.leave.index') }}" class="nav-link {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Izin & Cuti
                @php $pendingCount = \App\Models\LeaveRequest::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="nav-section">Laporan</div>
            <a href="{{ route('admin.reports.attendance') }}" class="nav-link {{ request()->routeIs('admin.reports.attendance') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Laporan Absensi
            </a>
            <a href="{{ route('admin.reports.payroll') }}" class="nav-link {{ request()->routeIs('admin.reports.payroll') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Laporan Gaji
            </a>
            <a href="{{ route('admin.reports.leave') }}" class="nav-link {{ request()->routeIs('admin.reports.leave') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data-fill"></i> Laporan Izin
            </a>
        @else
            <div class="nav-section">Menu</div>
            <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('karyawan.absensi.index') }}" class="nav-link {{ request()->routeIs('karyawan.absensi.*') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> Scan Absensi
            </a>
            <a href="{{ route('karyawan.leave.index') }}" class="nav-link {{ request()->routeIs('karyawan.leave.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Izin & Cuti
            </a>
            <a href="{{ route('karyawan.qrcode') }}" class="nav-link {{ request()->routeIs('karyawan.qrcode') ? 'active' : '' }}">
                <i class="bi bi-qr-code"></i> QR Code Saya
            </a>
            <a href="{{ route('karyawan.profile') }}" class="nav-link {{ request()->routeIs('karyawan.profile') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Profil
            </a>
        @endif
    </div>

    <!-- User info bottom sidebar -->
    <div class="mt-auto p-3" style="border-top:1px solid rgba(255,255,255,.08); margin-top:2rem;">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:#334155;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-person-fill text-white"></i>
            </div>
            <div>
                <div class="text-white fw-semibold" style="font-size:.85rem;">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem;color:#64748b;">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div id="main-content">
    <!-- TOPBAR -->
    <div id="topbar" class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h6 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h6>
                <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Dark Mode Toggle -->
            <button class="btn btn-sm btn-light" onclick="toggleDarkMode()" title="Dark Mode">
                <i class="bi bi-moon-fill" id="dark-icon"></i>
            </button>
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}

function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
    document.getElementById('dark-icon').className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
}

// Restore theme
(function() {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', saved);
    document.addEventListener('DOMContentLoaded', function() {
        const icon = document.getElementById('dark-icon');
        if (icon) icon.className = saved === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    });
})();

// Auto-dismiss alerts after 5s
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(a);
        bsAlert.close();
    });
}, 5000);
</script>
@stack('scripts')
</body>
</html>
