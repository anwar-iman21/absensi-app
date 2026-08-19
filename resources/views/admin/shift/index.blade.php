@extends('layouts.app')
@section('title', 'Shift Kerja')
@section('page-title', 'Manajemen Shift Kerja')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-2"></i>Tambah Shift
    </a>
</div>
<div class="row g-3">
    @forelse($shifts as $shift)
    <div class="col-md-4">
        <div class="page-card card p-4">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1">{{ $shift->nama_shift }}</h5>
                    <span class="badge bg-primary">{{ $shift->employees_count }} karyawan</span>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.shifts.edit', $shift->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                    <form method="POST" action="{{ route('admin.shifts.destroy', $shift->id) }}"
                          onsubmit="return confirm('Hapus shift ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
            </div>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div class="p-2 rounded" style="background:#f0fdf4;">
                        <div class="fw-bold text-success" style="font-size:1.1rem;">{{ substr($shift->jam_masuk,0,5) }}</div>
                        <div style="font-size:.65rem;color:#64748b;">Masuk</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded" style="background:#fef3c7;">
                        <div class="fw-bold text-warning" style="font-size:1.1rem;">{{ substr($shift->jam_pulang,0,5) }}</div>
                        <div style="font-size:.65rem;color:#64748b;">Pulang</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded" style="background:#fff1f2;">
                        <div class="fw-bold text-danger" style="font-size:1.1rem;">{{ $shift->toleransi_menit }}'</div>
                        <div style="font-size:.65rem;color:#64748b;">Toleransi</div>
                    </div>
                </div>
            </div>
            @if($shift->keterangan)
            <p class="text-muted small mt-2 mb-0">{{ $shift->keterangan }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-clock fs-1 d-block mb-2"></i>Belum ada shift kerja
    </div>
    @endforelse
</div>
@endsection
