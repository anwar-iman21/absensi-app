# 🏢 Sistem Absensi Karyawan Digital
**Laravel 10 + MySQL + QR Code/Barcode**

---

## ✅ Persyaratan

| Kebutuhan | Versi Minimum |
|-----------|---------------|
| PHP | 8.1+ |
| Laravel | 10.x |
| MySQL | 5.7+ / MariaDB 10.4+ |
| Composer | 2.x |

---

## 🚀 Instalasi Cepat (5 Langkah)

### 1. Salin project ke folder server
```bash
# Jika pakai XAMPP → taruh di: C:/xampp/htdocs/absensi-app
# Jika pakai Laragon → taruh di: C:/laragon/www/absensi-app
```

### 2. Install dependencies
```bash
cd absensi-app
composer install
```

### 3. Setup file .env
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`, sesuaikan bagian database:
```env
DB_DATABASE=absensi_db
DB_USERNAME=root
DB_PASSWORD=          # kosongkan jika tidak ada password
```

### 4. Import database
Buka **phpMyAdmin** → buat database `absensi_db` → tab **Import** → pilih `absensi_db.sql`

Atau via terminal:
```bash
mysql -u root -p absensi_db < absensi_db.sql
```

### 5. Storage link & jalankan
```bash
php artisan storage:link
php artisan serve
```

Buka: **http://localhost:8000** ✅

---

## 🔑 Akun Login

> Password semua: **`password123`**

| Role | Email |
|------|-------|
| Admin | admin@absensi.com |
| Karyawan | budi@absensi.com |
| Karyawan | siti@absensi.com |
| Karyawan | ahmad@absensi.com |
| Karyawan | dewi@absensi.com |
| Karyawan | rizky@absensi.com |

---

## 📁 Struktur Folder

```
absensi-app/
├── app/
│   ├── Console/Kernel.php
│   ├── Exceptions/Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EmployeeController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── PayrollController.php
│   │   │   │   ├── LeaveController.php
│   │   │   │   ├── ShiftController.php
│   │   │   │   └── ReportController.php
│   │   │   └── Karyawan/
│   │   │       ├── DashboardController.php
│   │   │       ├── AbsensiController.php
│   │   │       └── LeaveController.php
│   │   ├── Kernel.php
│   │   └── Middleware/
│   │       ├── Authenticate.php
│   │       ├── RoleMiddleware.php
│   │       ├── RedirectIfAuthenticated.php
│   │       └── ... (middleware bawaan Laravel)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Employee.php
│   │   ├── Attendance.php
│   │   ├── LeaveRequest.php
│   │   ├── Payroll.php
│   │   ├── Shift.php
│   │   ├── SalaryLog.php
│   │   └── ActivityLog.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       └── RouteServiceProvider.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── session.php
├── database/
│   └── seeders/DatabaseSeeder.php
├── public/
│   ├── index.php
│   └── .htaccess
├── resources/views/
│   ├── auth/login.blade.php
│   ├── layouts/app.blade.php
│   ├── admin/
│   │   ├── dashboard/index.blade.php
│   │   ├── employees/ (index, create, edit, show, kartu)
│   │   ├── attendance/ (index, scan)
│   │   ├── payroll/ (index, show, slip-pdf)
│   │   ├── leave/index.blade.php
│   │   ├── shift/ (index, create, edit)
│   │   └── reports/ (attendance, payroll, leave + PDF)
│   └── karyawan/
│       ├── dashboard/ (index, profile, qrcode)
│       ├── absensi/index.blade.php
│       └── leave/ (index, show)
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── bootstrap/app.php
├── absensi_db.sql      ← Import ini ke MySQL
├── .env.example
└── composer.json
```

---

## 🗃️ Tabel Database

| Tabel | Fungsi |
|-------|--------|
| `users` | Akun login admin & karyawan |
| `shifts` | Shift kerja (pagi/siang/malam) |
| `employees` | Data lengkap karyawan + barcode |
| `attendances` | Absensi harian |
| `leave_requests` | Pengajuan izin/sakit/cuti |
| `payrolls` | Jadwal & perhitungan gaji 30 hari |
| `salary_logs` | Riwayat pembayaran gaji |
| `activity_logs` | Log aktivitas user |

---

## ⚙️ Cara Kerja Sistem Gaji

```
Target gajian  = 30 hari kerja aktif
Hari kerja aktif = Hadir saja (izin/sakit/alfa/cuti tidak dihitung)

Estimasi gajian = tanggal_mulai + 30 hari + hari_mundur
hari_mundur     = izin + sakit + alfa + cuti

Contoh:
  Masuk penuh 30 hari         → gajian tepat waktu
  Izin 1 + Sakit 1 = 2 hari  → gajian mundur 2 hari
  Potongan = (gaji_pokok / 30) × hari_mundur
```

---

## 📦 Package yang Diinstall

```bash
composer require simplesoftwareio/simple-qrcode
composer require milon/barcode
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require yajra/laravel-datatables-oracle
```

---

## 🌐 Fitur Lengkap

### Admin
- ✅ Dashboard statistik + grafik absensi 7 hari
- ✅ CRUD karyawan lengkap + upload foto
- ✅ Scanner QR Code absensi (pakai kamera atau input manual)
- ✅ Input absensi manual
- ✅ Manajemen payroll & gaji otomatis
- ✅ Approve/reject pengajuan izin
- ✅ Manajemen shift kerja
- ✅ Laporan absensi, gaji, izin (export PDF)
- ✅ Cetak kartu ID karyawan

### Karyawan
- ✅ Dashboard info kehadiran & jadwal gaji
- ✅ Scan QR Code mandiri (absen masuk & pulang)
- ✅ Lihat QR Code pribadi + cetak
- ✅ Pengajuan izin/sakit/cuti + upload bukti
- ✅ Riwayat absensi lengkap
- ✅ Profil pribadi

---

## 🔧 Troubleshooting

**Error: Class not found setelah install**
```bash
composer dump-autoload
php artisan config:clear && php artisan cache:clear
```

**Error: SQLSTATE - koneksi MySQL gagal**
- Pastikan MySQL aktif
- Cek `DB_USERNAME` dan `DB_PASSWORD` di `.env`

**Error: storage/app/public tidak bisa diakses**
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

**QR Scanner tidak bisa akses kamera**
- Wajib pakai `https://` atau `localhost`
- Izinkan akses kamera di browser

**Foreign key error saat import SQL**
```sql
SET FOREIGN_KEY_CHECKS=0;
-- (import SQL)
SET FOREIGN_KEY_CHECKS=1;
```

---

## 💡 Tips

- **Barcode unik** dibuat otomatis format: `EMP001-XXXXXX`
- **QR Scanner** pakai library `html5-qrcode` (gratis, sudah include via CDN)
- **Dark mode** tersedia, klik ikon bulan di topbar
- **Responsive** untuk mobile dan desktop

---

*Sistem Absensi Digital v1.0 — Laravel 10*
