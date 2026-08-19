@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('page-title', 'Data Karyawan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan
    </a>
</div>

<div class="page-card card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, ID, jabatan..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')=='nonaktif' ? 'selected' : '' }}>Non-aktif</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Cari</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<div class="page-card card p-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Karyawan</h6>
        <span class="badge bg-primary">{{ $employees->total() }} karyawan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Karyawan</th>
                    <th>Jabatan / Divisi</th>
                    <th>Shift</th>
                    <th>Gaji Pokok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $i => $emp)
                <tr>
                    <td class="text-muted small">{{ $employees->firstItem() + $i }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;background:#e2e8f0;flex-shrink:0;">
                                @if($emp->foto)
                                    <img src="{{ asset('storage/foto-karyawan/'.$emp->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#4f46e5;color:#fff;font-size:.8rem;font-weight:700;">
                                        {{ strtoupper(substr($emp->nama_lengkap,0,1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $emp->nama_lengkap }}</div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $emp->employee_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $emp->jabatan }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $emp->divisi }}</div>
                    </td>
                    <td><span class="badge bg-secondary">{{ $emp->shift->nama_shift ?? '-' }}</span></td>
                    <td class="small fw-semibold">Rp {{ number_format($emp->gaji_pokok,0,',','.') }}</td>
                    <td>
                        <span class="badge {{ $emp->status_aktif == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($emp->status_aktif) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-sm btn-info text-white" title="Detail"><i class="bi bi-eye-fill"></i></a>
                            <a href="{{ route('admin.employees.edit', $emp->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                            <a href="{{ route('admin.employees.kartu', $emp->id) }}" class="btn btn-sm btn-secondary" title="Kartu" target="_blank"><i class="bi bi-credit-card-fill"></i></a>
                            <form method="POST" action="{{ route('admin.employees.destroy', $emp->id) }}"
                                  onsubmit="return confirm('Hapus karyawan {{ $emp->nama_lengkap }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>Belum ada data karyawan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $employees->links() }}</div>
</div>
@endsection
