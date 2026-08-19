<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size:12px; color:#1e293b; }
    .container { max-width:600px; margin:0 auto; padding:30px; }
    .header { background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; padding:20px 24px; border-radius:8px 8px 0 0; margin-bottom:0; }
    .header h2 { font-size:16px; font-weight:700; margin-bottom:2px; }
    .header p  { font-size:11px; opacity:.8; }
    .body { border:1px solid #e2e8f0; border-top:none; border-radius:0 0 8px 8px; padding:20px 24px; }
    .section-title { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:#64748b; font-weight:700; margin:14px 0 6px; border-bottom:1px solid #f1f5f9; padding-bottom:4px; }
    table { width:100%; border-collapse:collapse; }
    .info-table td { padding:4px 0; font-size:11px; }
    .info-table td:first-child { color:#64748b; width:40%; }
    .gaji-table td { padding:6px 0; border-bottom:1px solid #f1f5f9; }
    .gaji-table td:last-child { text-align:right; font-weight:600; }
    .total-row td { padding:10px 0; font-weight:700; font-size:13px; background:#f0fdf4; }
    .badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600; }
    .badge-success { background:#d1fae5; color:#065f46; }
    .badge-warning { background:#fef3c7; color:#92400e; }
    .footer { text-align:center; margin-top:20px; font-size:10px; color:#94a3b8; }
    .divider { border:none; border-top:2px dashed #e2e8f0; margin:14px 0; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>SLIP GAJI KARYAWAN</h2>
        <p>PT. Nama Perusahaan &nbsp;|&nbsp; Periode: {{ $payroll->periode_mulai->format('d/m/Y') }} — {{ $payroll->estimasi_gajian->format('d/m/Y') }}</p>
    </div>
    <div class="body">
        <div class="section-title">Data Karyawan</div>
        <table class="info-table">
            <tr><td>ID Karyawan</td><td>: {{ $payroll->employee->employee_id }}</td></tr>
            <tr><td>Nama</td><td>: {{ $payroll->employee->nama_lengkap }}</td></tr>
            <tr><td>Jabatan</td><td>: {{ $payroll->employee->jabatan }}</td></tr>
            <tr><td>Divisi</td><td>: {{ $payroll->employee->divisi }}</td></tr>
            <tr><td>Status Kerja</td><td>: {{ $payroll->employee->status_kerja }}</td></tr>
        </table>

        <div class="section-title">Rekap Kehadiran</div>
        <table class="info-table">
            <tr><td>Hadir</td><td>: {{ $payroll->total_hadir }} hari</td></tr>
            <tr><td>Izin</td><td>: {{ $payroll->total_izin }} hari</td></tr>
            <tr><td>Sakit</td><td>: {{ $payroll->total_sakit }} hari</td></tr>
            <tr><td>Alfa</td><td>: {{ $payroll->total_alfa }} hari</td></tr>
            <tr><td>Cuti</td><td>: {{ $payroll->total_cuti }} hari</td></tr>
            <tr><td>Hari Gajian Mundur</td><td>: +{{ $payroll->hari_mundur }} hari</td></tr>
        </table>

        <hr class="divider">

        <div class="section-title">Rincian Gaji</div>
        <table class="gaji-table">
            <tr>
                <td>Gaji Pokok</td>
                <td>Rp {{ number_format($payroll->gaji_pokok,0,',','.') }}</td>
            </tr>
            @if($payroll->bonus > 0)
            <tr>
                <td>Bonus</td>
                <td style="color:#16a34a;">+ Rp {{ number_format($payroll->bonus,0,',','.') }}</td>
            </tr>
            @endif
            <tr>
                <td>Potongan ({{ $payroll->hari_mundur }} hari tidak masuk)</td>
                <td style="color:#dc2626;">- Rp {{ number_format($payroll->potongan,0,',','.') }}</td>
            </tr>
            <tr class="total-row">
                <td style="padding-left:4px;">TOTAL DITERIMA</td>
                <td style="color:#16a34a;padding-right:4px;">Rp {{ number_format($payroll->total_gaji,0,',','.') }}</td>
            </tr>
        </table>

        <hr class="divider">

        <table class="info-table" style="margin-top:8px;">
            <tr>
                <td>Status Pembayaran</td>
                <td>:
                    <span class="badge {{ $payroll->status=='selesai' ? 'badge-success' : 'badge-warning' }}">
                        {{ strtoupper($payroll->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->translatedFormat('d F Y, H:i') }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Slip gaji ini dicetak secara otomatis oleh sistem.<br>
            Dokumen ini sah tanpa tanda tangan basah.</p>
        </div>
    </div>
</div>
</body>
</html>
