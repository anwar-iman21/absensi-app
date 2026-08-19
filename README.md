# Absensi App

**Sistem Absensi Karyawan Digital** — aplikasi berbasis web untuk mengelola kehadiran karyawan menggunakan QR Code/Barcode, lengkap dengan manajemen shift, cuti/izin, dan penggajian (payroll), dengan dua peran pengguna: **Admin** dan **Karyawan**.

## ✨ Fitur

### 👤 Admin
- **Manajemen Karyawan** — CRUD data karyawan, cetak kartu ID, dan generate QR Code per karyawan
- **Absensi** — pantau kehadiran, input absensi manual, serta scan QR/barcode untuk presensi
- **Payroll** — generate slip gaji otomatis berdasarkan data kehadiran, tandai status selesai, dan cetak slip gaji
- **Manajemen Cuti/Izin** — setujui atau tolak pengajuan cuti karyawan
- **Shift** — kelola jadwal shift kerja
- **Laporan** — ekspor laporan absensi (PDF/Excel), payroll (PDF), dan cuti
- **Activity Log** — pantau aktivitas pengguna dalam sistem

### 🧑‍💼 Karyawan
- **Absensi** — presensi masuk/pulang lewat scan QR Code
- **Pengajuan Cuti/Izin** — ajukan cuti dan lihat status persetujuannya
- **Profil & QR Code** — lihat profil pribadi dan QR Code presensi masing-masing

## 🛠️ Tech Stack

- **Framework:** Laravel 10
- **PHP:** ^8.1
- **Database:** MySQL
- **Frontend build:** Vite
- **Autentikasi:** Role-based middleware (Admin & Karyawan)
- **QR Code / Barcode:** simplesoftwareio/simple-qrcode, milon/barcode
- **Export laporan:** maatwebsite/excel, barryvdh/laravel-dompdf
- **Tabel data:** yajra/laravel-datatables-oracle

## 📂 Struktur Singkat

```
app/Http/Controllers/
  ├── Auth/                  → LoginController
  ├── Admin/                 → Employee, Attendance, Payroll, Leave, Shift, Report
  └── Karyawan/               → Dashboard, Absensi, Leave

app/Models/                  → Employee, Attendance, Payroll, LeaveRequest, Shift, SalaryLog, ActivityLog

database/migrations/         → skema tabel: employees, attendance, payroll, leave_requests, shifts, dll.
routes/web.php                → routing terpisah per role (admin/karyawan)
```

## 🚀 Cara Menjalankan

1. **Clone repo ini**
   ```bash
   git clone https://github.com/USERNAME/absensi-app.git
   cd absensi-app
   ```

2. **Install dependency**
   ```bash
   composer install
   npm install
   ```

3. **Siapkan file environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database**

   Buka `.env`, sesuaikan dengan database lokal kamu:

   ```env
   DB_DATABASE=absensi_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan migrasi (dan seeder jika tersedia)**
   ```bash
   php artisan migrate --seed
   ```

6. **Buat symlink storage** (untuk QR code/upload file)
   ```bash
   php artisan storage:link
   ```

7. **Jalankan server**
   ```bash
   php artisan serve
   npm run dev
   ```

   Buka `http://localhost:8000` di browser.

## 📌 Catatan

Project ini dikembangkan sebagai sistem absensi digital yang terintegrasi dengan fitur payroll dan manajemen cuti. Pemisahan hak akses Admin & Karyawan dilakukan melalui middleware role. Cocok dijadikan referensi belajar integrasi QR Code, export laporan, dan arsitektur Laravel skala menengah.

## 📄 Lisensi

Project ini open source untuk keperluan belajar, silakan digunakan sebagai referensi.
