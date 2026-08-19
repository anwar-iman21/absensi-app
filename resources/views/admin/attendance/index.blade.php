@extends('layouts.app')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')

@section('content')
<div class="page-card card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                @foreach(['hadir','izin','sakit','alfa','cuti'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Karyawan</label>
            <select name="employee_id" class="form-select">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Filter</button>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
            <a href="{{ route('admin.attendance.scan') }}" class="btn btn-success"><i class="bi bi-qr-code-scan"></i></a>
        </div>
    </form>
</div>

<!-- Manual Absen -->
<div class="page-card card p-3 mb-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square text-warning me-2"></i>Input Absensi Manual</h6>
    <form method="POST" action="{{ route('admin.attendance.manual') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Karyawan</label>
            <select name="employee_id" class="form-select form-select-sm" required>
                <option value="">Pilih...</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Tanggal</label>
            <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Status</label>
            <select name="status" class="form-select form-select-sm" required>
                @foreach(['hadir','izin','sakit','alfa','cuti'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Jam Masuk</label>
            <input type="time" name="jam_masuk" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Jam Pulang</label>
            <input type="time" name="jam_pulang" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-warning btn-sm w-100">Simpan</button>
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="page-card card p-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-table text-primary me-2"></i>
            Data Absensi — {{ request('tanggal') ? \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d F Y') : 'Hari Ini' }}
        </h6>
        <span class="badge bg-primary">{{ $attendances->total() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Terlambat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $abs)
                <tr>
                    <td class="text-muted small">{{ $attendances->firstItem() + $i }}</td>
                    <td>
                        <div class="fw-semibold small">{{ $abs->employee->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $abs->employee->employee_id }}</div>
                    </td>
                    <td class="small">{{ $abs->tanggal->translatedFormat('d M Y') }}</td>
                    <td class="small">
                        {{ $abs->jam_masuk ? substr($abs->jam_masuk,0,5) : '-' }}
                    </td>
                    <td class="small">{{ $abs->jam_pulang ? substr($abs->jam_pulang,0,5) : '-' }}</td>
                    <td>{!! $abs->status_badge !!}</td>
                    <td>
                        @if($abs->terlambat)
                            <span class="badge bg-warning text-dark">{{ $abs->menit_terlambat }} mnt</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.attendance.destroy', $abs->id) }}"
                              onsubmit="return confirm('Hapus data absensi ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash3-fill"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data absensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $attendances->links() }}</div>
</div>
@endsection
