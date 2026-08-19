@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi')

@section('content')
<div class="page-card card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <select name="bulan" class="form-select">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $bulan==$m ? 'selected':'' }}>
                        {{ \Carbon\Carbon::createFromDate(null,$m,1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" placeholder="Tahun">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tampilkan</button>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <a href="{{ route('admin.reports.attendance.pdf', request()->all()) }}" class="btn btn-outline-danger" target="_blank">
                <i class="bi bi-file-pdf-fill me-1"></i>PDF
            </a>
            <a href="{{ route('admin.reports.attendance.excel', request()->all()) }}" class="btn btn-outline-success">
                <i class="bi bi-file-excel-fill me-1"></i>Excel
            </a>
        </div>
    </form>
</div>

<div class="page-card card p-3">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-bar-chart-fill text-primary me-2"></i>
        Rekap Absensi — {{ \Carbon\Carbon::createFromDate($tahun,$bulan,1)->translatedFormat('F Y') }}
    </h6>
    <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
            <thead>
                <tr><th>Karyawan</th><th>Jabatan</th><th class="text-success">Hadir</th><th class="text-warning">Izin</th><th class="text-info">Sakit</th><th class="text-danger">Alfa</th><th class="text-secondary">Cuti</th><th>Terlambat</th></tr>
            </thead>
            <tbody>
                @forelse($rekap as $r)
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $r['employee']->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $r['employee']->employee_id }}</div>
                    </td>
                    <td class="small text-muted">{{ $r['employee']->jabatan }}</td>
                    <td><span class="badge bg-success">{{ $r['hadir'] }}</span></td>
                    <td><span class="badge bg-warning text-dark">{{ $r['izin'] }}</span></td>
                    <td><span class="badge bg-info">{{ $r['sakit'] }}</span></td>
                    <td><span class="badge bg-danger">{{ $r['alfa'] }}</span></td>
                    <td><span class="badge bg-secondary">{{ $r['cuti'] }}</span></td>
                    <td><span class="{{ $r['terlambat']>0 ? 'badge bg-warning text-dark' : 'text-muted small' }}">{{ $r['terlambat'] ?: '-' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
