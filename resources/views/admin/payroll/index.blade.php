@extends('layouts.app')
@section('title', 'Payroll & Gaji')
@section('page-title', 'Manajemen Payroll & Gaji')

@section('content')
<div class="page-card card p-3 mb-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-cash-stack text-success me-2"></i>Generate Payroll Baru</h6>
    </div>
    <form method="POST" action="{{ route('admin.payroll.generate') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Pilih Karyawan</label>
            <select name="employee_id" class="form-select" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach(\App\Models\Employee::where('status_aktif','aktif')->get() as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->nama_lengkap }} ({{ $emp->employee_id }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-plus-circle-fill me-1"></i>Buat Periode Gaji Baru
            </button>
        </div>
    </form>
</div>

<!-- Filter -->
<div class="page-card card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="proses" {{ request('status')=='proses' ? 'selected':'' }}>Proses</option>
                <option value="selesai" {{ request('status')=='selesai' ? 'selected':'' }}>Selesai</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="bulan" class="form-select">
                <option value="">Semua Bulan</option>
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ request('bulan')==$m ? 'selected':'' }}>
                        {{ \Carbon\Carbon::createFromDate(null,$m,1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="{{ request('tahun', date('Y')) }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
    </form>
</div>

<div class="page-card card p-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-table text-primary me-2"></i>Data Payroll</h6>
    <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Periode Mulai</th>
                    <th>Est. Gajian</th>
                    <th>Hadir</th>
                    <th>Tidak Masuk</th>
                    <th>Mundur</th>
                    <th>Total Gaji</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $p)
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $p->employee->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $p->employee->employee_id }}</div>
                    </td>
                    <td class="small">{{ $p->periode_mulai->format('d/m/Y') }}</td>
                    <td class="small">
                        {{ $p->estimasi_gajian->format('d/m/Y') }}
                        @if($p->estimasi_gajian->isPast() && $p->status=='proses')
                            <span class="badge bg-danger ms-1">Lewat</span>
                        @elseif($p->estimasi_gajian->isToday())
                            <span class="badge bg-success ms-1">Hari ini!</span>
                        @endif
                    </td>
                    <td><span class="badge bg-success">{{ $p->total_hadir }} hr</span></td>
                    <td>
                        <span class="badge bg-warning text-dark">{{ $p->total_izin + $p->total_sakit + $p->total_alfa + $p->total_cuti }} hr</span>
                    </td>
                    <td>
                        @if($p->hari_mundur > 0)
                            <span class="badge bg-danger">+{{ $p->hari_mundur }} hr</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="small fw-bold">Rp {{ number_format($p->total_gaji,0,',','.') }}</td>
                    <td>
                        <span class="badge {{ $p->status=='proses' ? 'bg-warning text-dark' : 'bg-success' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.payroll.show', $p->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('admin.payroll.cetak', $p->id) }}" class="btn btn-sm btn-secondary" title="Slip PDF" target="_blank">
                                <i class="bi bi-file-pdf-fill"></i>
                            </a>
                            @if($p->status=='proses')
                            <form method="POST" action="{{ route('admin.payroll.selesai', $p->id) }}"
                                  onsubmit="return confirm('Tandai gaji sudah dibayar?')">
                                @csrf @method('PUT')
                                <button class="btn btn-sm btn-success" title="Tandai Selesai">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada data payroll</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $payrolls->links() }}</div>
</div>
@endsection
