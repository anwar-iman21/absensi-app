@extends('layouts.app')
@section('title', 'QR Code Saya')
@section('page-title', 'QR Code Absensi Saya')

@section('content')
<div class="row justify-content-center">
<div class="col-md-5">
<div class="page-card card p-4 text-center">
    <h5 class="fw-bold mb-1">QR Code Absensi</h5>
    <p class="text-muted small mb-4">Tunjukkan QR Code ini kepada scanner untuk absensi masuk & pulang</p>

    <div class="p-4 rounded d-inline-block mb-3" style="background:#fff;border:2px solid #e2e8f0;border-radius:1rem;">
        <div id="qrcode"></div>
    </div>

    <div class="fw-bold mb-1">{{ $employee->nama_lengkap }}</div>
    <div class="text-muted small mb-1">{{ $employee->jabatan }} — {{ $employee->divisi }}</div>
    <div class="badge bg-secondary mb-3">{{ $employee->employee_id }}</div>

    <div class="p-2 rounded mb-3" style="background:#f8fafc;">
        <code style="font-size:.75rem;word-break:break-all;">{{ $employee->barcode }}</code>
    </div>

    <button onclick="window.print()" class="btn btn-outline-primary">
        <i class="bi bi-printer-fill me-2"></i>Cetak QR Code
    </button>
</div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("qrcode"), {
    text: "{{ $employee->barcode }}",
    width: 200, height: 200,
    colorDark: "#1e293b", colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
</script>
@endpush
