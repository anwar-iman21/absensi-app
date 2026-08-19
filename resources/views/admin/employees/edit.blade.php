@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Data Karyawan')

@section('content')
<div class="page-card card p-4">
    <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li class="small">{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-person-fill text-primary me-2"></i>Data Pribadi</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Foto Karyawan</label>
                    @if($employee->foto)
                        <div class="mb-2">
                            <img src="{{ asset('storage/foto-karyawan/'.$employee->foto) }}" id="preview"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:.5rem;border:2px solid #e2e8f0;">
                        </div>
                    @else
                        <img id="preview" src="" style="width:80px;height:80px;object-fit:cover;border-radius:.5rem;border:2px solid #e2e8f0;display:none;">
                    @endif
                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(this)">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $employee->nama_lengkap) }}" required>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" {{ $employee->jenis_kelamin=='Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $employee->jenis_kelamin=='Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $employee->tanggal_lahir?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="form-label small fw-semibold">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $employee->no_hp) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $employee->alamat) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status Aktif</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="aktif" {{ $employee->status_aktif=='aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $employee->status_aktif=='nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-briefcase-fill text-success me-2"></i>Data Pekerjaan</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">ID Karyawan</label>
                    <input type="text" class="form-control bg-light" value="{{ $employee->employee_id }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $employee->jabatan) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Divisi <span class="text-danger">*</span></label>
                    <input type="text" name="divisi" class="form-control" value="{{ old('divisi', $employee->divisi) }}" required>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Status Kerja</label>
                        <select name="status_kerja" class="form-select">
                            @foreach(['Tetap','Kontrak','Magang','Freelance'] as $s)
                                <option value="{{ $s }}" {{ $employee->status_kerja==$s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Shift Kerja</label>
                        <select name="shift_id" class="form-select" required>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ $employee->shift_id==$shift->id ? 'selected' : '' }}>
                                    {{ $shift->nama_shift }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="form-label small fw-semibold">Gaji Pokok <span class="text-danger">*</span></label>
                    <input type="number" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', $employee->gaji_pokok) }}" required>
                </div>

                <h6 class="fw-bold mt-4 mb-3 pb-2 border-bottom"><i class="bi bi-lock-fill text-warning me-2"></i>Ubah Password</h6>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru <span class="text-muted">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min 6 karakter">
                </div>
            </div>
        </div>

        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save-fill me-2"></i>Simpan Perubahan</button>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('preview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
