@extends('layouts.app')
@section('title', 'Laporan Gaji')
@section('page-title', 'Laporan Gaji')

@section('content')
<div class="page-card card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" placeholder="Tahun">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tampilkan</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.reports.payroll.pdf', ['tahun'=>$tahun]) }}" class="btn btn-outline-danger" target="_blank">
                <i class="bi bi-file-pdf-fill me-1"></i>Export PDF
            </a>
        </div>
    </form>
</div>

<div class="page-card card p-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-cash-stack text-success me-2"></i>Laporan Gaji Tahun {{ $tahun }}</h6>
    <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
            <thead>
                <tr><th>Karyawan</th><th>Periode</th><th>Hadir</th><th>Potongan</th><th>Total Gaji</th><th>Status</th></tr>
            </thead>
            <tbody>
                @php $totalGaji = 0; @endphp
                @forelse($payrolls as $p)
                @php $totalGaji += $p->total_gaji; @endphp
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $p->employee->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $p->employee->employee_id }}</div>
                    </td>
                    <td class="small">{{ $p->periode_mulai->format('d/m/Y') }} — {{ $p->estimasi_gajian->format('d/m/Y') }}</td>
                    <td><span class="badge bg-success">{{ $p->total_hadir }} hr</span></td>
                    <td class="small text-danger">- Rp {{ number_format($p->potongan,0,',','.') }}</td>
                    <td class="fw-bold small">Rp {{ number_format($p->total_gaji,0,',','.') }}</td>
                    <td><span class="badge {{ $p->status=='selesai' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($p->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada data</td></tr>
                @endforelse
                @if($payrolls->count() > 0)
                <tr style="background:#f0fdf4;">
                    <td colspan="4" class="fw-bold text-end">TOTAL PENGELUARAN GAJI:</td>
                    <td class="fw-bold text-success">Rp {{ number_format($totalGaji,0,',','.') }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
