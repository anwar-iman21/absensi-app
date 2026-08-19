@extends('layouts.app')
@section('title', 'Edit Shift')
@section('page-title', 'Edit Shift Kerja')

@section('content')
<div class="row justify-content-center">
<div class="col-md-6">
<div class="page-card card p-4">
    <form method="POST" action="{{ route('admin.shifts.update', $shift->id) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Shift <span class="text-danger">*</span></label>
            <input type="text" name="nama_shift" class="form-control" value="{{ old('nama_shift', $shift->nama_shift) }}" required>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <label class="form-label small fw-semibold">Jam Masuk</label>
                <input type="time" name="jam_masuk" class="form-control" value="{{ old('jam_masuk', substr($shift->jam_masuk,0,5)) }}" required>
            </div>
            <div class="col-6">
                <label class="form-label small fw-semibold">Jam Pulang</label>
                <input type="time" name="jam_pulang" class="form-control" value="{{ old('jam_pulang', substr($shift->jam_pulang,0,5)) }}" required>
            </div>
        </div>
        <div class="mb-3 mt-2">
            <label class="form-label small fw-semibold">Toleransi (menit)</label>
            <input type="number" name="toleransi_menit" class="form-control" value="{{ old('toleransi_menit', $shift->toleransi_menit) }}" min="0" max="120" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $shift->keterangan) }}">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Simpan</button>
            <a href="{{ route('admin.shifts.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
</div>
</div>
@endsection
