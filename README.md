# SIM PKL Laduny

**Sistem Informasi Manajemen PKL (Praktik Kerja Lapangan)** — aplikasi berbasis web untuk mengelola kegiatan PKL/magang siswa, mulai dari absensi, jurnal harian, penilaian, hingga monitoring ibadah, dengan dua peran pengguna: **Admin** dan **Siswa**.

## ✨ Fitur

### 👤 Admin
- **Master Data** — kelola Sekolah, Jurusan, Batch, Lokasi PKL, dan Kelompok
- **Manajemen Siswa** — CRUD data siswa peserta PKL, termasuk cetak kartu peserta
- **Absensi** — pantau, setujui, atau tolak absensi & pengajuan izin siswa
- **Monitoring Ibadah** — approve/reject laporan ibadah siswa, lengkap dengan rekap
- **Jurnal Harian** — review dan verifikasi jurnal kegiatan siswa
- **Penilaian** — input dan kelola nilai PKL siswa
- **Pengumuman & Pesan** — kirim pengumuman dan pesan langsung ke siswa
- **Laporan** — ekspor laporan absensi, ibadah, jurnal, dan penilaian ke **Excel**, **CSV**, dan **PDF**
- **Activity Log** — pantau aktivitas pengguna dalam sistem
- **Pengaturan** — konfigurasi umum sistem

### 🎓 Siswa
- **Absensi** — presensi masuk/pulang, termasuk pengajuan izin
- **Ibadah** — input laporan ibadah harian
- **Jurnal Harian** — tulis dan edit jurnal kegiatan PKL
- **Penilaian** — lihat hasil penilaian dari admin/pembimbing
- **Pengumuman & Pesan** — baca pengumuman dan kirim/terima pesan
- **Profil** — kelola data diri dan ubah password

## 🛠️ Tech Stack

- **Framework:** Laravel 10
- **PHP:** ^8.1
- **Templating:** Blade
- **Database:** MySQL
- **Frontend build:** Vite
- **Autentikasi:** Role-based middleware (Admin & Siswa)
- **Export laporan:** Excel, CSV, PDF (custom helper: `SimpleXlsx`, `SimplePdf`)
- **Validasi lokasi absensi:** `GeoHelper`

## 📂 Struktur Singkat

```
app/Http/Controllers/
  ├── Auth/                  → LoginController
  ├── Admin/                 → controller khusus admin (Sekolah, Jurusan, Siswa, Absensi, dll.)
  └── Siswa/                 → controller khusus siswa (Absensi, Jurnal, Penilaian, dll.)

app/Models/                  → Siswa, Absensi, JurnalHarian, Penilaian, IbadahMonitoring, dll.
app/Helpers/GeoHelper.php    → validasi lokasi untuk absensi
app/Support/                 → SimpleXlsx.php, SimplePdf.php (util export laporan)

database/migrations/         → skema tabel: sekolah, jurusan, batch, siswa, absensi, dll.
routes/web.php                → routing utama, dipisah per role (admin/siswa)
```

## 🚀 Cara Menjalankan

1. **Clone repo ini**
   ```bash
   git clone https://github.com/USERNAME/sim-pkl-laduny.git
   cd sim-pkl-laduny
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
   Buka `.env`, sesuaikan bagian berikut dengan database lokal kamu:
   ```
   APP_URL=http://localhost:8000
   DB_DATABASE=sim_pkl_laduny
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > Catatan: `.env.example` bawaan disetel untuk mode `production`. Untuk pengembangan lokal, ubah `APP_ENV=local` dan `APP_DEBUG=true` supaya lebih mudah melakukan debugging.

5. **Jalankan migrasi (dan seeder jika tersedia)**
   ```bash
   php artisan migrate --seed
   ```

6. **Buat symlink storage** (untuk file upload, jika digunakan)
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

Project ini dikembangkan sebagai sistem manajemen PKL/magang siswa yang terintegrasi. Fitur berbasis role (Admin & Siswa) memisahkan hak akses secara jelas melalui middleware. Cocok dijadikan referensi belajar arsitektur Laravel skala menengah dengan banyak modul yang saling terhubung.

## 📄 Lisensi

Project ini open source untuk keperluan belajar, silakan digunakan sebagai referensi.
