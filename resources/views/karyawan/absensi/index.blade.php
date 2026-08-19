@extends('layouts.app')
@section('title', 'Scan Absensi')
@section('page-title', 'Absensi Saya')

@section('content')
<div class="row g-3">
    <div class="col-md-5">
        <div class="page-card card p-4 text-center">
            <h6 class="fw-bold mb-1"><i class="bi bi-qr-code-scan text-primary me-2"></i>Scan QR Code</h6>
            <p class="text-muted small mb-3">Scan QR Code Anda untuk absensi masuk atau pulang</p>

            @if($hariIni)
                <div class="alert {{ $hariIni->jam_pulang ? 'alert-success' : 'alert-info' }} small">
                    @if($hariIni->jam_pulang)
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Anda sudah absen masuk ({{ substr($hariIni->jam_masuk,0,5) }}) dan pulang ({{ substr($hariIni->jam_pulang,0,5) }}) hari ini.
                    @else
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Sudah absen masuk pukul {{ substr($hariIni->jam_masuk,0,5) }}. Scan lagi untuk absen pulang.
                    @endif
                </div>
            @endif

            <div class="mb-3 fw-bold fs-4" id="clock">--:--:--</div>
            <div id="qr-reader" style="width:100%;max-width:320px;margin:0 auto;border-radius:1rem;overflow:hidden;border:3px solid #4f46e5;"></div>
            <div id="scan-result" class="mt-3 d-none"></div>

            <div class="mt-3">
                <p class="text-muted small">Atau masukkan barcode manual:</p>
                <div class="input-group">
                    <input type="text" id="barcode-input" class="form-control" placeholder="Scan / ketik barcode...">
                    <button class="btn btn-primary" onclick="kirimScan()"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="page-card card p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i>Riwayat Absensi</h6>
            <div class="table-responsive">
                <table class="table table-modern table-sm mb-0">
                    <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Ket</th></tr></thead>
                    <tbody>
                        @forelse($absensi as $abs)
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
            <div class="mt-3">{{ $absensi->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
setInterval(() => {
    document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID');
}, 1000);

const myBarcode = "{{ $employee->barcode }}";
let isProcessing = false;

html5QrCode = new Html5Qrcode("qr-reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: { width: 200, height: 200 } },
    (text) => { if (!isProcessing && text === myBarcode) kirimScan(text); },
    () => {}
).catch(() => {
    document.getElementById('qr-reader').innerHTML =
        '<div class="p-3 text-muted small"><i class="bi bi-camera-video-off d-block fs-2 mb-2"></i>Kamera tidak tersedia. Gunakan input manual.</div>';
});

async function kirimScan(barcode = null) {
    const kode = barcode || document.getElementById('barcode-input').value.trim();
    if (!kode) return;
    isProcessing = true;

    const res   = await fetch('{{ route("karyawan.absensi.scan") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ barcode: kode })
    });
    const data = await res.json();

    const box = document.getElementById('scan-result');
    box.className = `mt-3 alert ${data.success ? (data.type==='masuk'?'alert-success':'alert-info') : 'alert-warning'}`;
    box.innerHTML = `<i class="bi bi-${data.success ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>${data.message}`;
    box.classList.remove('d-none');

    if (data.success) {
        Swal.fire({ icon: data.type==='masuk'?'success':'info', title: data.type==='masuk'?'Absen Masuk!':'Absen Pulang!', text: data.message, timer: 2500, showConfirmButton: false });
        setTimeout(() => location.reload(), 2600);
    }
    document.getElementById('barcode-input').value = '';
    setTimeout(() => { isProcessing = false; }, 3000);
}

document.getElementById('barcode-input').addEventListener('keypress', e => {
    if (e.key === 'Enter') kirimScan();
});
</script>
@endpush
