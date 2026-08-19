<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Karyawan — {{ $employee->nama_lengkap }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #e2e8f0; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Segoe UI', sans-serif; flex-direction: column; gap: 1.5rem; }

        .kartu {
            width: 340px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }

        /* Sisi Depan */
        .kartu-depan {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            padding: 0;
        }
        .kartu-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .kartu-header .logo {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .kartu-header h4 { font-size: .95rem; font-weight: 700; margin: 0; }
        .kartu-header p  { font-size: .7rem; opacity: .7; margin: 0; }

        .kartu-body { padding: 20px; }
        .foto-wrapper {
            width: 80px; height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,.2);
            overflow: hidden;
            margin: 0 auto 12px;
            background: #334155;
            display: flex; align-items: center; justify-content: center;
        }
        .foto-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .foto-inisial { font-size: 2rem; font-weight: 700; color: #fff; }

        .kartu-nama { text-align: center; margin-bottom: 16px; }
        .kartu-nama h3 { font-size: 1rem; font-weight: 700; margin-bottom: 2px; }
        .kartu-nama p  { font-size: .75rem; color: #94a3b8; }

        .kartu-info { border-top: 1px solid rgba(255,255,255,.1); padding-top: 12px; }
        .kartu-info-row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .kartu-info-row .lbl { font-size: .65rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
        .kartu-info-row .val { font-size: .75rem; font-weight: 600; }

        .kartu-footer {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .kartu-footer .id-badge {
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .1em;
        }

        /* Sisi Belakang */
        .kartu-belakang {
            background: #fff;
            padding: 20px;
            text-align: center;
        }
        .kartu-belakang h5 { font-size: .85rem; color: #1e293b; font-weight: 700; margin-bottom: 4px; }
        .kartu-belakang p  { font-size: .7rem; color: #64748b; margin-bottom: 12px; }
        #qr-back { display: flex; justify-content: center; margin-bottom: 10px; }
        .barcode-text { font-size: .65rem; color: #94a3b8; word-break: break-all; margin-bottom: 12px; }
        .kartu-belakang .note {
            font-size: .65rem;
            color: #ef4444;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }

        /* Print button */
        .print-btn {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: .6rem 2rem;
            border-radius: .5rem;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
        }
        .print-btn:hover { background: #3730a3; }

        @media print {
            body { background: #fff; display: block; padding: 20px; }
            .print-btn { display: none; }
            .kartu { box-shadow: none; margin: 0 auto 20px; }
        }
    </style>
</head>
<body>

<!-- Kartu Depan -->
<div class="kartu">
    <div class="kartu-depan">
        <div class="kartu-header">
            <div class="logo">🏢</div>
            <div>
                <h4>PT. Nama Perusahaan</h4>
                <p>Kartu Identitas Karyawan</p>
            </div>
        </div>
        <div class="kartu-body">
            <div class="foto-wrapper">
                @if($employee->foto)
                    <img src="{{ asset('storage/foto-karyawan/'.$employee->foto) }}">
                @else
                    <span class="foto-inisial">{{ strtoupper(substr($employee->nama_lengkap,0,1)) }}</span>
                @endif
            </div>
            <div class="kartu-nama">
                <h3>{{ $employee->nama_lengkap }}</h3>
                <p>{{ $employee->jabatan }} • {{ $employee->divisi }}</p>
            </div>
            <div class="kartu-info">
                <div class="kartu-info-row">
                    <div>
                        <div class="lbl">Tgl. Bergabung</div>
                        <div class="val" style="color:#94a3b8;">{{ $employee->tanggal_masuk->format('d/m/Y') }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="lbl">Status Kerja</div>
                        <div class="val" style="color:#22c55e;">{{ $employee->status_kerja }}</div>
                    </div>
                </div>
                <div class="kartu-info-row">
                    <div>
                        <div class="lbl">Shift</div>
                        <div class="val" style="color:#94a3b8;">{{ $employee->shift->nama_shift ?? '-' }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="lbl">Divisi</div>
                        <div class="val" style="color:#94a3b8;">{{ $employee->divisi }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kartu-footer">
            <span class="id-badge">{{ $employee->employee_id }}</span>
            <span style="font-size:.65rem;opacity:.6;">{{ now()->year }}</span>
        </div>
    </div>
</div>

<!-- Kartu Belakang -->
<div class="kartu">
    <div class="kartu-belakang">
        <h5>Scan untuk Absensi</h5>
        <p>Gunakan QR Code ini untuk absensi masuk & pulang</p>
        <div id="qr-back"></div>
        <div class="barcode-text">{{ $employee->barcode }}</div>
        <div class="note">
            ⚠️ Kartu ini bersifat rahasia. Jangan dipinjamkan kepada orang lain.<br>
            Jika hilang, segera laporkan ke bagian HRD.
        </div>
    </div>
</div>

<button class="print-btn" onclick="window.print()">🖨️ Cetak Kartu</button>

<script>
new QRCode(document.getElementById("qr-back"), {
    text: "{{ $employee->barcode }}",
    width: 160, height: 160,
    colorDark: "#1e293b", colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
</script>
</body>
</html>
