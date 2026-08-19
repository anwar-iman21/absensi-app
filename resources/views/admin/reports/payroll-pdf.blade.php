<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family:'DejaVu Sans',sans-serif; font-size:11px; color:#1e293b; }
    .header { background:#1e293b; color:#fff; padding:14px 20px; margin-bottom:16px; }
    .header h2 { font-size:14px; margin:0 0 2px; }
    .header p  { font-size:10px; opacity:.7; margin:0; }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:#16a34a; color:#fff; }
    thead th { padding:7px 8px; font-size:10px; }
    tbody tr:nth-child(even) { background:#f8fafc; }
    tbody td { padding:6px 8px; border-bottom:1px solid #e2e8f0; text-align:center; }
    tbody td:nth-child(2) { text-align:left; }
    .total-row { background:#f0fdf4; font-weight:700; }
    .footer { margin-top:20px; text-align:center; font-size:9px; color:#94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h2>LAPORAN PENGGAJIAN KARYAWAN</h2>
    <p>Tahun: {{ $tahun }} &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
</div>
<table>
    <thead>
        <tr><th>#</th><th style="text-align:left;">Nama Karyawan</th><th>Periode</th><th>Hadir</th><th>Potongan</th><th>Total Gaji</th><th>Status</th></tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($payrolls as $i => $p)
        @php $total += $p->total_gaji; @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td style="text-align:left;">{{ $p->employee->nama_lengkap }}</td>
            <td>{{ $p->periode_mulai->format('d/m/Y') }}</td>
            <td>{{ $p->total_hadir }} hr</td>
            <td>Rp {{ number_format($p->potongan,0,',','.') }}</td>
            <td>Rp {{ number_format($p->total_gaji,0,',','.') }}</td>
            <td>{{ ucfirst($p->status) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="5" style="text-align:right;padding-right:12px;">TOTAL:</td>
            <td>Rp {{ number_format($total,0,',','.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
<div class="footer">Dokumen ini dicetak otomatis oleh Sistem Absensi Digital</div>
</body>
</html>
