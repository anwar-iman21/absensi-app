@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')

@section('content')
<div class="row g-3">
    <!-- Profil Card -->
    <div class="col-md-4">
        <div class="page-card card p-4 text-center">
            <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;margin:0 auto 1rem;border:3px solid #4f46e5;">
                @if($employee->foto)
                    <img src="{{ asset('storage/foto-karyawan/'.$employee->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="width:100%;height:100%;background:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:#fff;font-weight:700;">
                        {{ strtoupper(substr($employee->nama_lengkap,0,1)) }}
                    </div>
                @endif
            </div>
            <h5 class="fw-bold mb-1">{{ $employee->nama_lengkap }}</h5>
            <p class="text-muted small mb-1">{{ $employee->jabatan }}</p>
            <span class="badge bg-primary mb-2">{{ $employee->divisi }}</span>
            <div class="mb-3">
                <span class="badge {{ $employee->status_aktif=='aktif' ? 'bg-success' : 'bg-secondary' }} me-1">{{ ucfirst($employee->status_aktif) }}</span>
                <span class="badge bg-secondary">{{ $employee->status_kerja }}</span>
            </div>

            <!-- QR Code -->
            <div class="p-3 rounded mb-3" style="background:#f8fafc;">
                <p class="text-muted small mb-2">QR Code Absensi</p>
                <div id="qr-container"></div>
                <div class="text-muted mt-1" style="font-size:.7rem;word-break:break-all;">{{ $employee->barcode }}</div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('admin.employees.kartu', $employee->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-credit-card-fill me-1"></i>Cetak Kartu ID
                </a>
                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-fill me-1"></i>Edit Data
                </a>
            </div>
        </div>
    </div>

    <!-- Info Detail -->
    <div class="col-md-8">
        <div class="page-card card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-info-circle-fill text-primary me-2"></i>Informasi Karyawan</h6>
            <div class="row g-3">
                @php
                $infos = [
                    ['label'=>'ID Karyawan','value'=>$employee->employee_id],
                    ['label'=>'Email','value'=>$employee->email ?? '-'],
                    ['label'=>'No. HP','value'=>$employee->no_hp ?? '-'],
                    ['label'=>'Jenis Kelamin','value'=>$employee->jenis_kelamin ?? '-'],
                    ['label'=>'Tanggal Lahir','value'=>$employee->tanggal_lahir?->translatedFormat('d F Y') ?? '-'],
                    ['label'=>'Alamat','value'=>$employee->alamat ?? '-'],
                    ['label'=>'Tanggal Masuk','value'=>$employee->tanggal_masuk->translatedFormat('d F Y')],
                    ['label'=>'Shift Kerja','value'=>$employee->shift->nama_shift ?? '-'],
                    ['label'=>'Gaji Pokok','value'=>'Rp '.number_format($employee->gaji_pokok,0,',','.')],
                ];
                @endphp
                @foreach($infos as $info)
                <div class="col-md-6">
                    <div class="text-muted small">{{ $info['label'] }}</div>
                    <div class="fw-semibold small">{{ $info['value'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Statistik Kehadiran Bulan Ini -->
        <div class="page-card card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-bar-chart-fill text-success me-2"></i>Kehadiran Bulan Ini</h6>
            @php
            $bulan = now()->month; $tahun = now()->year;
            $abs = $employee->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->get();
            $stats = ['hadir'=>$abs->where('status','hadir')->count(),'izin'=>$abs->where('status','izin')->count(),'sakit'=>$abs->where('status','sakit')->count(),'alfa'=>$abs->where('status','alfa')->count(),'cuti'=>$abs->where('status','cuti')->count(),'terlambat'=>$abs->where('terlambat',1)->count()];
            @endphp
            <div class="row g-2 text-center">
                @foreach([['hadir','success'],['izin','warning'],['sakit','info'],['alfa','danger'],['cuti','secondary'],['terlambat','dark']] as [$key,$color])
                <div class="col-4 col-md-2">
                    <div class="p-2 rounded" style="background:#f8fafc;">
                        <div class="fw-bold fs-4 text-{{ $color }}">{{ $stats[$key] }}</div>
                        <div class="text-muted" style="font-size:.7rem;">{{ ucfirst($key) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Riwayat Absensi -->
        <div class="page-card card p-4">
            <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-clock-history text-info me-2"></i>Riwayat Absensi Terakhir</h6>
            <div class="table-responsive">
                <table class="table table-modern table-sm mb-0">
                    <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Ket</th></tr></thead>
                    <tbody>
                        @forelse($employee->attendances as $abs)
                        <tr>
                            <td class="small">{{ $abs->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="small">{{ $abs->jam_masuk ? substr($abs->jam_masuk,0,5) : '-' }}</td>
                            <td class="small">{{ $abs->jam_pulang ? substr($abs->jam_pulang,0,5) : '-' }}</td>
                            <td>{!! $abs->status_badge !!}</td>
                            <td class="small text-muted">{{ $abs->terlambat ? $abs->menit_terlambat.' mnt' : '' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data absensi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("qr-container"), {
    text: "{{ $employee->barcode }}",
    width: 120, height: 120,
    colorDark: "#1e293b", colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
</script>
@endpush
