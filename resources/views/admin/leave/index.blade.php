@extends('layouts.app')
@section('title', 'Kelola Izin & Cuti')
@section('page-title', 'Pengajuan Izin & Cuti')

@section('content')
<div class="page-card card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending"   {{ request('status')=='pending'   ? 'selected':'' }}>Menunggu</option>
                <option value="disetujui" {{ request('status')=='disetujui' ? 'selected':'' }}>Disetujui</option>
                <option value="ditolak"   {{ request('status')=='ditolak'   ? 'selected':'' }}>Ditolak</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
    </form>
</div>

<div class="page-card card p-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text-fill text-warning me-2"></i>Daftar Pengajuan Izin</h6>
    <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
            <thead>
                <tr><th>Karyawan</th><th>Jenis</th><th>Tanggal</th><th>Hari</th><th>Keterangan</th><th>Bukti</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $leave->employee->nama_lengkap }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $leave->employee->employee_id }}</div>
                    </td>
                    <td><span class="badge bg-info">{{ ucfirst($leave->jenis) }}</span></td>
                    <td class="small">
                        {{ $leave->tanggal_mulai->format('d/m/Y') }}
                        @if($leave->jumlah_hari > 1) — {{ $leave->tanggal_akhir->format('d/m/Y') }} @endif
                    </td>
                    <td><span class="badge bg-secondary">{{ $leave->jumlah_hari }} hr</span></td>
                    <td class="small text-muted">{{ Str::limit($leave->keterangan, 40) }}</td>
                    <td>
                        @if($leave->file_bukti)
                            <a href="{{ asset('storage/dokumen-izin/'.$leave->file_bukti) }}" target="_blank" class="btn btn-xs btn-outline-info" style="font-size:.72rem;padding:.2rem .5rem;">
                                <i class="bi bi-file-earmark-fill"></i> Lihat
                            </a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>{!! $leave->status_badge !!}</td>
                    <td>
                        @if($leave->status == 'pending')
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('admin.leave.approve', $leave->id) }}">
                                @csrf @method('PUT')
                                <button class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Setuju</button>
                            </form>
                            <form method="POST" action="{{ route('admin.leave.reject', $leave->id) }}">
                                @csrf @method('PUT')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i> Tolak</button>
                            </form>
                        </div>
                        @else
                            <span class="text-muted small">{{ $leave->approvedBy->name ?? '-' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada pengajuan izin</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $leaves->links() }}</div>
</div>
@endsection
