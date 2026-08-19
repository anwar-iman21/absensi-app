@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan Baru')

@section('content')
<div class="page-card card p-4">
    <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data">
        @csrf

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)<li class="small">{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="row g-3">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-person-fill text-primary me-2"></i>Data Pribadi</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Foto Karyawan</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*" onchange="previewFoto(this)">
                    <div class="mt-2">
                        <img id="preview" src="" alt="" style="width:80px;height:80px;object-fit:cover;border-radius:.5rem;border:2px solid #e2e8f0;display:none;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                           value="{{ old('nama_lengkap') }}" required>
                    @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">Pilih...</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin')=='Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin')=='Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                    </div>
                </div>

                <div class="mb-3 mt-2">
                    <label class="form-label small fw-semibold">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-briefcase-fill text-success me-2"></i>Data Pekerjaan</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                           value="{{ old('jabatan') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Divisi <span class="text-danger">*</span></label>
                    <input type="text" name="divisi" class="form-control @error('divisi') is-invalid @enderror"
                           value="{{ old('divisi') }}" required>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Status Kerja <span class="text-danger">*</span></label>
                        <select name="status_kerja" class="form-select" required>
                            @foreach(['Tetap','Kontrak','Magang','Freelance'] as $s)
                                <option value="{{ $s }}" {{ old('status_kerja')==$s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Gaji Pokok <span class="text-danger">*</span></label>
                        <input type="number" name="gaji_pokok" class="form-control @error('gaji_pokok') is-invalid @enderror"
                               value="{{ old('gaji_pokok') }}" min="0" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Shift Kerja <span class="text-danger">*</span></label>
                        <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                            <option value="">Pilih Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id')==$shift->id ? 'selected' : '' }}>
                                    {{ $shift->nama_shift }} ({{ substr($shift->jam_masuk,0,5) }}-{{ substr($shift->jam_pulang,0,5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-3 pb-2 border-bottom"><i class="bi bi-lock-fill text-warning me-2"></i>Akun Login</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min 6 karakter" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save-fill me-2"></i>Simpan Karyawan
            </button>
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
