@extends('layouts.app')
@section('title', 'Laporan Izin')
@section('page-title', 'Laporan Izin & Cuti')

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
            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tampilkan</button>
        </div>
    </form>
</div>

<div class="page-card card p-3">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-clipboard-data-fill text-info me-2"></i>
        Laporan Izin — {{ \Carbon\Carbon::createFromDate($tahun,$bulan,1)->translatedFormat('F Y') }}
    </h6>
    <div class="table-responsive">
        <table class="table table-modern mb-0">
            <thead><tr><th>Karyawan</th><th>Jenis</th><th>Tanggal</th><th>Hari</th><th>Keterangan</th><th>Status</th><th>Diproses Oleh</th></tr></thead>
            <tbody>
                @forelse($leaves as $leave)
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $leave->employee->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $leave->employee->employee_id }}</div>
                    </td>
                    <td><span class="badge bg-info">{{ ucfirst($leave->jenis) }}</span></td>
                    <td class="small">{{ $leave->tanggal_mulai->format('d/m/Y') }}@if($leave->jumlah_hari>1) — {{ $leave->tanggal_akhir->format('d/m/Y') }}@endif</td>
                    <td><span class="badge bg-secondary">{{ $leave->jumlah_hari }}</span></td>
                    <td class="small text-muted">{{ Str::limit($leave->keterangan,40) }}</td>
                    <td>{!! $leave->status_badge !!}</td>
                    <td class="small text-muted">{{ $leave->approvedBy->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
