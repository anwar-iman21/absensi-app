@extends('layouts.app')
@section('title', 'Izin & Cuti')
@section('page-title', 'Pengajuan Izin & Cuti')

@section('content')
<div class="row g-3">
    <!-- Form Pengajuan -->
    <div class="col-md-5">
        <div class="page-card card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Ajukan Izin Baru</h6>
            <form method="POST" action="{{ route('karyawan.leave.store') }}" enctype="multipart/form-data">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jenis Izin <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                        <option value="">Pilih jenis...</option>
                        <option value="izin"  {{ old('jenis')=='izin'  ? 'selected':'' }}>🙋 Izin</option>
                        <option value="sakit" {{ old('jenis')=='sakit' ? 'selected':'' }}>🤒 Sakit</option>
                        <option value="cuti"  {{ old('jenis')=='cuti'  ? 'selected':'' }}>🏖️ Cuti</option>
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Dari Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                               value="{{ old('tanggal_mulai') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Sampai Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror"
                               value="{{ old('tanggal_akhir') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="form-label small fw-semibold">Keterangan <span class="text-danger">*</span></label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                              rows="3" placeholder="Jelaskan alasan izin..." required>{{ old('keterangan') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Upload Bukti <span class="text-muted">(opsional)</span></label>
                    <input type="file" name="file_bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text">Surat izin / surat dokter (JPG, PNG, PDF max 2MB)</div>
                </div>

                <div class="p-3 rounded mb-3" style="background:#fffbeb;">
                    <div class="small text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Perhatian:</strong> Setiap hari izin yang disetujui akan memundurkan jadwal gajian Anda.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send-fill me-2"></i>Kirim Pengajuan
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat -->
    <div class="col-md-7">
        <div class="page-card card p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i>Riwayat Pengajuan</h6>
            @forelse($leaves as $leave)
            <div class="p-3 rounded mb-2" style="background:#f8fafc;border-left:3px solid {{ $leave->status=='disetujui' ? '#22c55e' : ($leave->status=='ditolak' ? '#ef4444' : '#f59e0b') }};">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-info me-1">{{ ucfirst($leave->jenis) }}</span>
                        {!! $leave->status_badge !!}
                        <div class="small mt-1">
                            {{ $leave->tanggal_mulai->format('d/m/Y') }}
                            @if($leave->jumlah_hari > 1) — {{ $leave->tanggal_akhir->format('d/m/Y') }} @endif
                            <span class="text-muted">({{ $leave->jumlah_hari }} hari)</span>
                        </div>
                        <div class="text-muted small">{{ Str::limit($leave->keterangan, 60) }}</div>
                        @if($leave->catatan_admin)
                            <div class="small text-danger mt-1"><i class="bi bi-chat-fill me-1"></i>{{ $leave->catatan_admin }}</div>
                        @endif
                    </div>
                    <div class="text-muted small">{{ $leave->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada pengajuan izin
            </div>
            @endforelse
            <div class="mt-2">{{ $leaves->links() }}</div>
        </div>
    </div>
</div>
@endsection
