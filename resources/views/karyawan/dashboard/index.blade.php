@extends('layouts.app')
@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard Saya')

@section('content')
<!-- Greeting -->
<div class="page-card card p-4 mb-3" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;">
    <div class="d-flex align-items-center gap-3">
        <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:#fff;flex-shrink:0;">
            @if($employee->foto)
                <img src="{{ asset('storage/foto-karyawan/'.$employee->foto) }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
            @else
                {{ strtoupper(substr($employee->nama_lengkap,0,1)) }}
            @endif
        </div>
        <div class="text-white">
            <h5 class="fw-bold mb-1">Halo, {{ explode(' ', $employee->nama_lengkap)[0] }}! 👋</h5>
            <p class="mb-0 opacity-75 small">{{ $employee->jabatan }} • {{ $employee->divisi }}</p>
        </div>
        <div class="ms-auto text-end text-white">
            <div class="fw-bold fs-5" id="clock">--:--</div>
            <div class="opacity-75 small">{{ now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>
</div>

<!-- Status absensi hari ini -->
<div class="page-card card p-3 mb-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-clock-fill text-primary me-2"></i>Absensi Hari Ini</h6>
    @if($absensiHariIni)
        <div class="row g-2 text-center">
            <div class="col-6">
                <div class="p-3 rounded" style="background:#f0fdf4;">
                    <div class="fw-bold text-success fs-5">
                        {{ $absensiHariIni->jam_masuk ? substr($absensiHariIni->jam_masuk,0,5) : '-' }}
                    </div>
                    <div class="text-muted small">Jam Masuk</div>
                    @if($absensiHariIni->terlambat)
                        <span class="badge bg-warning text-dark mt-1">Terlambat {{ $absensiHariIni->menit_terlambat }} mnt</span>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded" style="background:#eff6ff;">
                    <div class="fw-bold text-primary fs-5">
                        {{ $absensiHariIni->jam_pulang ? substr($absensiHariIni->jam_pulang,0,5) : '-' }}
                    </div>
                    <div class="text-muted small">Jam Pulang</div>
                    @if(!$absensiHariIni->jam_pulang && $absensiHariIni->jam_masuk)
                        <span class="badge bg-info text-white mt-1">Belum pulang</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-3 text-muted">
            <i class="bi bi-qr-code fs-1 d-block mb-2 text-primary"></i>
            <p class="mb-2">Belum absen hari ini</p>
            <a href="{{ route('karyawan.absensi.index') }}" class="btn btn-primary">
                <i class="bi bi-qr-code-scan me-2"></i>Scan Absensi Sekarang
            </a>
        </div>
    @endif
</div>

<!-- Stats bulan ini -->
<div class="row g-3 mb-3">
    @foreach([['Hadir','hadir','success','check-circle-fill'],['Izin','izin','warning','calendar-x'],['Sakit','sakit','info','thermometer-half'],['Alfa','alfa','danger','x-circle-fill'],['Terlambat','terlambat','dark','clock-fill']] as [$lbl,$key,$color,$icon])
    <div class="col-6 col-md">
        <div class="stat-card card text-center p-3">
            <i class="bi bi-{{ $icon }} text-{{ $color }} mb-1 fs-4"></i>
            <div class="fw-bold fs-4">{{ $stats[$key] }}</div>
            <div class="text-muted small">{{ $lbl }}</div>
        </div>
    </div>
    @endforeach
</div>

<!-- Info Gajian -->
@if($payroll)
<div class="page-card card p-4 mb-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-cash-stack text-success me-2"></i>Info Jadwal Gajian</h6>
    <div class="row g-3 align-items-center">
        <div class="col-md-4 text-center">
            <div class="fw-bold" style="font-size:2rem;color:#4f46e5;">
                {{ max(0, $payroll->target_hari_kerja - $payroll->total_hadir) }}
            </div>
            <div class="text-muted small">Hari lagi menuju gajian</div>
        </div>
        <div class="col-md-8">
            <div class="d-flex justify-content-between small mb-1">
                <span>Progress Hari Kerja</span>
                <span class="fw-semibold">{{ $payroll->total_hadir }} / {{ $payroll->target_hari_kerja }} hari</span>
            </div>
            <div class="progress mb-2" style="height:12px;border-radius:10px;">
                <div class="progress-bar bg-primary" style="width:{{ min(100,round($payroll->total_hadir/$payroll->target_hari_kerja*100)) }}%;border-radius:10px;"></div>
            </div>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div class="small text-muted">Estimasi Gajian</div>
                    <div class="fw-bold small text-success">{{ $payroll->estimasi_gajian->format('d/m/Y') }}</div>
                </div>
                <div class="col-4">
                    <div class="small text-muted">Hari Mundur</div>
                    <div class="fw-bold small text-danger">+{{ $payroll->hari_mundur }} hari</div>
                </div>
                <div class="col-4">
                    <div class="small text-muted">Est. Gaji</div>
                    <div class="fw-bold small">Rp {{ number_format($payroll->total_gaji,0,',','.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Riwayat Absensi -->
<div class="page-card card p-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-info me-2"></i>Riwayat Absensi Terbaru</h6>
        <a href="{{ route('karyawan.absensi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-modern table-sm mb-0">
            <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($recentAbsensi as $abs)
                <tr>
                    <td class="small">{{ $abs->tanggal->translatedFormat('d M') }}</td>
                    <td class="small">{{ $abs->jam_masuk ? substr($abs->jam_masuk,0,5) : '-' }}</td>
                    <td class="small">{{ $abs->jam_pulang ? substr($abs->jam_pulang,0,5) : '-' }}</td>
                    <td>{!! $abs->status_badge !!}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3 small">Belum ada riwayat absensi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
setInterval(() => {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
},1000);
</script>
@endpush
