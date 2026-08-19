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
    thead tr { background:#4f46e5; color:#fff; }
    thead th { padding:7px 8px; font-size:10px; text-align:left; }
    tbody tr:nth-child(even) { background:#f8fafc; }
    tbody td { padding:6px 8px; border-bottom:1px solid #e2e8f0; }
    .footer { margin-top:20px; text-align:center; font-size:9px; color:#94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h2>LAPORAN ABSENSI KARYAWAN</h2>
    <p>Periode: {{ $namaBulan }} &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
</div>
<table>
    <thead>
        <tr><th>#</th><th>ID</th><th>Nama Karyawan</th><th>Jabatan</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alfa</th><th>Cuti</th><th>Terlambat</th></tr>
    </thead>
    <tbody>
        @foreach($rekap as $i => $r)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['employee']->employee_id }}</td>
            <td>{{ $r['employee']->nama_lengkap }}</td>
            <td>{{ $r['employee']->jabatan }}</td>
            <td>{{ $r['hadir'] }}</td>
            <td>{{ $r['izin'] }}</td>
            <td>{{ $r['sakit'] }}</td>
            <td>{{ $r['alfa'] }}</td>
            <td>{{ $r['cuti'] }}</td>
            <td>{{ $r['terlambat'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">Dokumen ini dicetak otomatis oleh Sistem Absensi Digital</div>
</body>
</html>
