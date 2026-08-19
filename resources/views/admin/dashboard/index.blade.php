@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card card bg-primary text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="background:rgba(255,255,255,.2)"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_karyawan'] }}</div>
                    <div class="stat-label text-white-50">Total Karyawan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card card bg-success text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="background:rgba(255,255,255,.2)"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['hadir_hari_ini'] }}</div>
                    <div class="stat-label text-white-50">Hadir Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card card bg-warning text-dark">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="background:rgba(0,0,0,.1)"><i class="bi bi-calendar-x-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['izin_hari_ini'] }}</div>
                    <div class="stat-label">Izin/Sakit</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card card bg-danger text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="background:rgba(255,255,255,.2)"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['alfa_hari_ini'] }}</div>
                    <div class="stat-label text-white-50">Alfa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card card bg-info text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="background:rgba(255,255,255,.2)"><i class="bi bi-clock-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['terlambat_hari_ini'] }}</div>
                    <div class="stat-label text-white-50">Terlambat</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card card text-white" style="background:#8b5cf6;">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box" style="background:rgba(255,255,255,.2)"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['pending_izin'] }}</div>
                    <div class="stat-label text-white-50">Izin Pending</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Grafik Absensi 7 Hari -->
    <div class="col-md-8">
        <div class="page-card card p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Absensi 7 Hari Terakhir</h6>
            </div>
            <canvas id="chartAbsensi" height="100"></canvas>
        </div>
    </div>
    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="page-card card p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-lightning-fill text-warning me-2"></i>Aksi Cepat</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.attendance.scan') }}" class="btn btn-primary">
                    <i class="bi bi-qr-code-scan me-2"></i>Buka Scanner QR
                </a>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-success">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan
                </a>
                <a href="{{ route('admin.leave.index') }}" class="btn btn-warning">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>Kelola Izin
                    @if($stats['pending_izin'] > 0)
                        <span class="badge bg-danger">{{ $stats['pending_izin'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reports.attendance') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-download me-2"></i>Export Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Absensi Hari Ini -->
    <div class="col-md-7">
        <div class="page-card card p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar-check-fill text-success me-2"></i>Absensi Hari Ini</h6>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            @if($recentAttendances->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada absensi hari ini
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendances as $abs)
                        <tr>
                            <td>
                                <div class="fw-semibold small">{{ $abs->employee->nama_lengkap }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $abs->employee->jabatan }}</div>
                            </td>
                            <td class="small">{{ $abs->jam_masuk ? substr($abs->jam_masuk,0,5) : '-' }}
                                @if($abs->terlambat) <span class="badge bg-warning text-dark ms-1">Lambat</span> @endif
                            </td>
                            <td class="small">{{ $abs->jam_pulang ? substr($abs->jam_pulang,0,5) : '-' }}</td>
                            <td>{!! $abs->status_badge !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Izin Pending -->
    <div class="col-md-5">
        <div class="page-card card p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-hourglass-split text-warning me-2"></i>Izin Menunggu Approval</h6>
                <a href="{{ route('admin.leave.index') }}" class="btn btn-sm btn-outline-warning">Kelola</a>
            </div>
            @if($pendingLeaves->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-all fs-1 d-block mb-2 text-success"></i>Tidak ada pengajuan pending
                </div>
            @else
                @foreach($pendingLeaves as $leave)
                <div class="d-flex align-items-start gap-2 mb-3 p-2 rounded" style="background:#fffbeb;">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $leave->employee->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ ucfirst($leave->jenis) }} • {{ $leave->tanggal_mulai->format('d/m/Y') }}
                            @if($leave->jumlah_hari > 1) — {{ $leave->tanggal_akhir->format('d/m/Y') }} @endif
                            ({{ $leave->jumlah_hari }} hari)
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <form method="POST" action="{{ route('admin.leave.approve', $leave->id) }}" class="d-inline">
                            @csrf @method('PUT')
                            <button class="btn btn-xs btn-success" style="font-size:.7rem;padding:.2rem .5rem;" title="Approve">✓</button>
                        </form>
                        <form method="POST" action="{{ route('admin.leave.reject', $leave->id) }}" class="d-inline">
                            @csrf @method('PUT')
                            <button class="btn btn-xs btn-danger" style="font-size:.7rem;padding:.2rem .5rem;" title="Tolak">✗</button>
                        </form>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const labels = @json(array_column($chartData, 'tanggal'));
const hadir  = @json(array_column($chartData, 'hadir'));
const alfa   = @json(array_column($chartData, 'alfa'));
const izin   = @json(array_column($chartData, 'izin'));

new Chart(document.getElementById('chartAbsensi'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Hadir',    data: hadir, backgroundColor: '#22c55e', borderRadius: 6 },
            { label: 'Izin/Sakit', data: izin, backgroundColor: '#f59e0b', borderRadius: 6 },
            { label: 'Alfa',     data: alfa,  backgroundColor: '#ef4444', borderRadius: 6 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { x: { stacked: false }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush
