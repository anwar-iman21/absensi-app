@extends('layouts.app')
@section('title', 'Scan Absensi')
@section('page-title', 'Scan Absensi QR Code')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="page-card card p-4 text-center">
            <h5 class="fw-bold mb-1"><i class="bi bi-qr-code-scan text-primary me-2"></i>Scanner Absensi</h5>
            <p class="text-muted small mb-3">Arahkan kamera ke QR Code karyawan untuk absensi masuk/pulang</p>

            <!-- Jam real-time -->
            <div class="mb-3 p-2 rounded" style="background:#f1f5f9;">
                <span class="fw-bold fs-4" id="clock">--:--:--</span>
                <div class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>

            <!-- Video preview -->
            <div id="qr-reader" style="width:100%;max-width:400px;margin:0 auto;border-radius:1rem;overflow:hidden;border:3px solid #4f46e5;"></div>

            <!-- Status box -->
            <div id="scan-result" class="mt-3 p-3 rounded d-none"></div>

            <!-- Manual input -->
            <div class="mt-4">
                <p class="text-muted small">Atau input barcode manual:</p>
                <div class="input-group">
                    <input type="text" id="manual-barcode" class="form-control" placeholder="Ketik/scan barcode...">
                    <button class="btn btn-primary" onclick="prosesBarcode(document.getElementById('manual-barcode').value)">
                        <i class="bi bi-send-fill"></i> Proses
                    </button>
                </div>
            </div>

            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary mt-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Absensi
            </a>
        </div>
    </div>

    <!-- Log scan hari ini -->
    <div class="col-md-8 col-lg-6 mt-3">
        <div class="page-card card p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i>Log Scan Hari Ini</h6>
            <div id="scan-log">
                <div class="text-center text-muted py-3 small">Belum ada scan hari ini</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// Clock
setInterval(() => {
    document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID');
}, 1000);
document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID');

const logContainer = document.getElementById('scan-log');
let scanLogs = [];
let isProcessing = false;

// Inisialisasi QR Scanner
const html5QrCode = new Html5Qrcode("qr-reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: { width: 250, height: 250 } },
    (decodedText) => {
        if (!isProcessing) prosesBarcode(decodedText);
    },
    () => {}
).catch(err => {
    document.getElementById('qr-reader').innerHTML =
        '<div class="p-4 text-warning"><i class="bi bi-camera-video-off fs-1"></i><p class="mt-2 small">Kamera tidak dapat diakses. Gunakan input manual di bawah.</p></div>';
});

async function prosesBarcode(barcode) {
    if (!barcode.trim()) return;
    isProcessing = true;

    const resultBox = document.getElementById('scan-result');
    resultBox.className = 'mt-3 p-3 rounded';
    resultBox.innerHTML = '<div class="spinner-border spinner-border-sm text-primary me-2"></div> Memproses...';
    resultBox.classList.remove('d-none');

    try {
        const res = await fetch('{{ route("admin.attendance.process_scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ barcode })
        });
        const data = await res.json();

        if (data.success) {
            const isIn = data.type === 'masuk';
            resultBox.className = `mt-3 p-3 rounded alert ${isIn ? 'alert-success' : 'alert-info'}`;
            resultBox.innerHTML = `<i class="bi bi-${isIn ? 'check-circle-fill' : 'box-arrow-right'} me-2"></i>${data.message.replace(/\n/g,'<br>')}`;

            // Tambah ke log
            scanLogs.unshift({
                nama: data.data.nama,
                jam: data.data.jam,
                type: data.type,
                terlambat: data.data.terlambat
            });
            renderLog();

            // Suara notif
            try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAA...').play(); } catch(e){}

            Swal.fire({
                icon: isIn ? 'success' : 'info',
                title: isIn ? 'Absen Masuk!' : 'Absen Pulang!',
                text: `${data.data.nama} — ${data.data.jam}`,
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            resultBox.className = 'mt-3 p-3 rounded alert alert-warning';
            resultBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${data.message}`;
        }
    } catch (e) {
        resultBox.className = 'mt-3 p-3 rounded alert alert-danger';
        resultBox.innerHTML = '<i class="bi bi-wifi-off me-2"></i>Gagal terhubung ke server.';
    }

    document.getElementById('manual-barcode').value = '';
    setTimeout(() => { isProcessing = false; }, 2000);
}

function renderLog() {
    if (scanLogs.length === 0) return;
    logContainer.innerHTML = scanLogs.map(l => `
        <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background:#f8fafc;">
            <span class="badge ${l.type === 'masuk' ? 'bg-success' : 'bg-info'}">${l.type === 'masuk' ? 'MASUK' : 'PULANG'}</span>
            <span class="fw-semibold small">${l.nama}</span>
            <span class="text-muted small ms-auto">${l.jam}</span>
            ${l.terlambat ? '<span class="badge bg-warning text-dark">Terlambat</span>' : ''}
        </div>
    `).join('');
}

// Enter key di manual input
document.getElementById('manual-barcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') prosesBarcode(this.value);
});
</script>
@endpush
