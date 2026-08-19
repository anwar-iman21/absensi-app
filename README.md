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

DB_DATABASE=absensi_app
DB_USERNAME=root
DB_PASSWORD=


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
