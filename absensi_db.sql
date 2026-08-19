-- ============================================================
-- DATABASE: SISTEM ABSENSI KARYAWAN DIGITAL
-- Laravel 10 | MySQL 5.7+
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+07:00";

CREATE DATABASE IF NOT EXISTS `absensi_db`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `absensi_db`;

-- ------------------------------------------------------------
-- 1. users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255) NOT NULL,
  `email`             VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password`          VARCHAR(255) NOT NULL,
  `role`              ENUM('admin','karyawan') NOT NULL DEFAULT 'karyawan',
  `remember_token`    VARCHAR(100) DEFAULT NULL,
  `created_at`        TIMESTAMP NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. shifts
-- ------------------------------------------------------------
CREATE TABLE `shifts` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_shift`       VARCHAR(100) NOT NULL,
  `jam_masuk`        TIME NOT NULL,
  `jam_pulang`       TIME NOT NULL,
  `toleransi_menit`  INT NOT NULL DEFAULT 15,
  `keterangan`       VARCHAR(255) DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. employees
-- ------------------------------------------------------------
CREATE TABLE `employees` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        BIGINT UNSIGNED NOT NULL,
  `employee_id`    VARCHAR(20) NOT NULL,
  `nama_lengkap`   VARCHAR(255) NOT NULL,
  `foto`           VARCHAR(255) DEFAULT NULL,
  `jabatan`        VARCHAR(100) NOT NULL,
  `divisi`         VARCHAR(100) NOT NULL,
  `no_hp`          VARCHAR(20) DEFAULT NULL,
  `email`          VARCHAR(255) DEFAULT NULL,
  `alamat`         TEXT DEFAULT NULL,
  `tanggal_lahir`  DATE DEFAULT NULL,
  `jenis_kelamin`  ENUM('Laki-laki','Perempuan') DEFAULT NULL,
  `tanggal_masuk`  DATE NOT NULL,
  `status_kerja`   ENUM('Tetap','Kontrak','Magang','Freelance') NOT NULL DEFAULT 'Tetap',
  `gaji_pokok`     DECIMAL(15,2) NOT NULL DEFAULT 0,
  `shift_id`       BIGINT UNSIGNED DEFAULT NULL,
  `barcode`        VARCHAR(100) NOT NULL,
  `status_aktif`   ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at`     TIMESTAMP NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  UNIQUE KEY `employees_barcode_unique` (`barcode`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_shift_id_foreign` (`shift_id`),
  CONSTRAINT `employees_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_shift_id_foreign`
    FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. attendances
-- ------------------------------------------------------------
CREATE TABLE `attendances` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id`      BIGINT UNSIGNED NOT NULL,
  `tanggal`          DATE NOT NULL,
  `jam_masuk`        TIME DEFAULT NULL,
  `jam_pulang`       TIME DEFAULT NULL,
  `status`           ENUM('hadir','izin','sakit','alfa','cuti') NOT NULL DEFAULT 'hadir',
  `terlambat`        TINYINT(1) NOT NULL DEFAULT 0,
  `menit_terlambat`  INT NOT NULL DEFAULT 0,
  `keterangan`       TEXT DEFAULT NULL,
  `device_scan`      VARCHAR(100) DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_employee_tanggal_unique` (`employee_id`,`tanggal`),
  KEY `attendances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `attendances_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. leave_requests
-- ------------------------------------------------------------
CREATE TABLE `leave_requests` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id`    BIGINT UNSIGNED NOT NULL,
  `jenis`          ENUM('izin','sakit','cuti') NOT NULL,
  `tanggal_mulai`  DATE NOT NULL,
  `tanggal_akhir`  DATE NOT NULL,
  `jumlah_hari`    INT NOT NULL DEFAULT 1,
  `keterangan`     TEXT DEFAULT NULL,
  `file_bukti`     VARCHAR(255) DEFAULT NULL,
  `status`         ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `approved_by`    BIGINT UNSIGNED DEFAULT NULL,
  `catatan_admin`  TEXT DEFAULT NULL,
  `created_at`     TIMESTAMP NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_employee_id_foreign` (`employee_id`),
  KEY `leave_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `leave_requests_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_requests_approved_by_foreign`
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. payrolls
-- ------------------------------------------------------------
CREATE TABLE `payrolls` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id`         BIGINT UNSIGNED NOT NULL,
  `periode_mulai`       DATE NOT NULL,
  `periode_akhir`       DATE DEFAULT NULL,
  `estimasi_gajian`     DATE NOT NULL,
  `total_hari_kerja`    INT NOT NULL DEFAULT 0,
  `target_hari_kerja`   INT NOT NULL DEFAULT 30,
  `total_hadir`         INT NOT NULL DEFAULT 0,
  `total_izin`          INT NOT NULL DEFAULT 0,
  `total_sakit`         INT NOT NULL DEFAULT 0,
  `total_alfa`          INT NOT NULL DEFAULT 0,
  `total_cuti`          INT NOT NULL DEFAULT 0,
  `hari_mundur`         INT NOT NULL DEFAULT 0,
  `gaji_pokok`          DECIMAL(15,2) NOT NULL DEFAULT 0,
  `potongan`            DECIMAL(15,2) NOT NULL DEFAULT 0,
  `bonus`               DECIMAL(15,2) NOT NULL DEFAULT 0,
  `total_gaji`          DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status`              ENUM('proses','selesai','dibatalkan') NOT NULL DEFAULT 'proses',
  `catatan`             TEXT DEFAULT NULL,
  `created_at`          TIMESTAMP NULL DEFAULT NULL,
  `updated_at`          TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payrolls_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payrolls_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. salary_logs
-- ------------------------------------------------------------
CREATE TABLE `salary_logs` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payroll_id`    BIGINT UNSIGNED NOT NULL,
  `employee_id`   BIGINT UNSIGNED NOT NULL,
  `tanggal_bayar` DATE NOT NULL,
  `jumlah`        DECIMAL(15,2) NOT NULL,
  `metode`        VARCHAR(100) DEFAULT 'Transfer Bank',
  `keterangan`    TEXT DEFAULT NULL,
  `dibuat_oleh`   BIGINT UNSIGNED DEFAULT NULL,
  `created_at`    TIMESTAMP NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_logs_payroll_id_foreign` (`payroll_id`),
  KEY `salary_logs_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_logs_payroll_id_foreign`
    FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_logs_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. activity_logs
-- ------------------------------------------------------------
CREATE TABLE `activity_logs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED DEFAULT NULL,
  `aksi`       VARCHAR(255) NOT NULL,
  `model`      VARCHAR(100) DEFAULT NULL,
  `model_id`   BIGINT UNSIGNED DEFAULT NULL,
  `deskripsi`  TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. password_reset_tokens
-- ------------------------------------------------------------
CREATE TABLE `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. sessions
-- ------------------------------------------------------------
CREATE TABLE `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED DEFAULT NULL,
  `ip_address`    VARCHAR(45) DEFAULT NULL,
  `user_agent`    TEXT DEFAULT NULL,
  `payload`       LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. cache
-- ------------------------------------------------------------
CREATE TABLE `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key`        VARCHAR(255) NOT NULL,
  `owner`      VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 12. jobs
-- ------------------------------------------------------------
CREATE TABLE `jobs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(255) NOT NULL,
  `payload`      LONGTEXT NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL,
  `reserved_at`  INT UNSIGNED DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at`   INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA SEEDER
-- Password semua akun: password123
-- ============================================================

INSERT INTO `shifts` (`id`,`nama_shift`,`jam_masuk`,`jam_pulang`,`toleransi_menit`,`keterangan`,`created_at`,`updated_at`) VALUES
(1,'Shift Pagi', '07:00:00','15:00:00',15,'Shift kerja pagi hari', NOW(),NOW()),
(2,'Shift Siang','12:00:00','20:00:00',15,'Shift kerja siang hari',NOW(),NOW()),
(3,'Shift Malam','20:00:00','04:00:00',15,'Shift kerja malam hari',NOW(),NOW());

INSERT INTO `users` (`id`,`name`,`email`,`password`,`role`,`created_at`,`updated_at`) VALUES
(1,'Administrator', 'admin@absensi.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',   NOW(),NOW()),
(2,'Budi Santoso',  'budi@absensi.com',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','karyawan',NOW(),NOW()),
(3,'Siti Rahayu',   'siti@absensi.com',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','karyawan',NOW(),NOW()),
(4,'Ahmad Fauzi',   'ahmad@absensi.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','karyawan',NOW(),NOW()),
(5,'Dewi Kusuma',   'dewi@absensi.com',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','karyawan',NOW(),NOW()),
(6,'Rizky Pratama', 'rizky@absensi.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','karyawan',NOW(),NOW());

INSERT INTO `employees` (`id`,`user_id`,`employee_id`,`nama_lengkap`,`jabatan`,`divisi`,`no_hp`,`email`,`alamat`,`tanggal_lahir`,`jenis_kelamin`,`tanggal_masuk`,`status_kerja`,`gaji_pokok`,`shift_id`,`barcode`,`status_aktif`,`created_at`,`updated_at`) VALUES
(1,2,'EMP001','Budi Santoso', 'Staff IT',        'IT',         '081234567890','budi@absensi.com', 'Jl. Merdeka No.1 Jakarta',    '1990-05-15','Laki-laki','2022-01-03','Tetap',   5000000.00,1,'EMP001-ABC123','aktif',NOW(),NOW()),
(2,3,'EMP002','Siti Rahayu',  'Staff Keuangan',  'Keuangan',   '081234567891','siti@absensi.com', 'Jl. Sudirman No.2 Bandung',   '1992-08-20','Perempuan','2022-02-01','Tetap',   4500000.00,1,'EMP002-DEF456','aktif',NOW(),NOW()),
(3,4,'EMP003','Ahmad Fauzi',  'Supervisor',      'Operasional','081234567892','ahmad@absensi.com','Jl. Gatot Subroto No.3 Bekasi','1988-12-10','Laki-laki','2021-06-15','Tetap',   7000000.00,2,'EMP003-GHI789','aktif',NOW(),NOW()),
(4,5,'EMP004','Dewi Kusuma',  'Staff HRD',       'HRD',        '081234567893','dewi@absensi.com', 'Jl. Ahmad Yani No.4 Surabaya','1995-03-25','Perempuan','2023-03-01','Kontrak', 3800000.00,1,'EMP004-JKL012','aktif',NOW(),NOW()),
(5,6,'EMP005','Rizky Pratama','Staff Marketing', 'Marketing',  '081234567894','rizky@absensi.com','Jl. Diponegoro No.5 Medan',   '1993-07-07','Laki-laki','2022-09-01','Tetap',   4800000.00,3,'EMP005-MNO345','aktif',NOW(),NOW());

INSERT INTO `attendances` (`employee_id`,`tanggal`,`jam_masuk`,`jam_pulang`,`status`,`terlambat`,`menit_terlambat`,`keterangan`,`device_scan`,`created_at`,`updated_at`) VALUES
(1,DATE_SUB(CURDATE(),INTERVAL 10 DAY),'07:05:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 9  DAY),'07:20:00','15:00:00','hadir',1,5,'Sedikit terlambat','QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 8  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 7  DAY),NULL,NULL,'izin',0,0,'Urusan keluarga','Manual Admin',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 6  DAY),'07:10:00','15:05:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 5  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 4  DAY),'08:30:00','15:00:00','hadir',1,90,'Macet parah','QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 3  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 2  DAY),NULL,NULL,'sakit',0,0,'Demam','Manual Admin',NOW(),NOW()),
(1,DATE_SUB(CURDATE(),INTERVAL 1  DAY),'07:05:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 10 DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 9  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 8  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 7  DAY),'07:05:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 6  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 5  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 4  DAY),NULL,NULL,'alfa',0,0,NULL,'Manual Admin',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 3  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 2  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 1  DAY),'07:00:00','15:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 10 DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 9  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 8  DAY),'12:20:00','20:00:00','hadir',1,20,'Ban bocor','QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 7  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 6  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 5  DAY),NULL,NULL,'cuti',0,0,'Cuti tahunan','Manual Admin',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 4  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 3  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 2  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 1  DAY),'12:00:00','20:00:00','hadir',0,0,NULL,'QR Scanner',NOW(),NOW());

INSERT INTO `leave_requests` (`employee_id`,`jenis`,`tanggal_mulai`,`tanggal_akhir`,`jumlah_hari`,`keterangan`,`status`,`approved_by`,`created_at`,`updated_at`) VALUES
(1,'izin', DATE_SUB(CURDATE(),INTERVAL 7 DAY),DATE_SUB(CURDATE(),INTERVAL 7 DAY),1,'Urusan keluarga','disetujui',1,NOW(),NOW()),
(1,'sakit',DATE_SUB(CURDATE(),INTERVAL 2 DAY),DATE_SUB(CURDATE(),INTERVAL 2 DAY),1,'Demam','disetujui',1,NOW(),NOW()),
(2,'alfa', DATE_SUB(CURDATE(),INTERVAL 4 DAY),DATE_SUB(CURDATE(),INTERVAL 4 DAY),1,NULL,'pending',NULL,NOW(),NOW()),
(3,'cuti', DATE_SUB(CURDATE(),INTERVAL 5 DAY),DATE_SUB(CURDATE(),INTERVAL 5 DAY),1,'Cuti tahunan','disetujui',1,NOW(),NOW()),
(4,'izin', DATE_ADD(CURDATE(),INTERVAL 2 DAY),DATE_ADD(CURDATE(),INTERVAL 3 DAY),2,'Keperluan pribadi','pending',NULL,NOW(),NOW());

INSERT INTO `payrolls` (`employee_id`,`periode_mulai`,`estimasi_gajian`,`total_hari_kerja`,`target_hari_kerja`,`total_hadir`,`total_izin`,`total_sakit`,`total_alfa`,`total_cuti`,`hari_mundur`,`gaji_pokok`,`potongan`,`bonus`,`total_gaji`,`status`,`created_at`,`updated_at`) VALUES
(1,DATE_SUB(CURDATE(),INTERVAL 10 DAY),DATE_ADD(CURDATE(),INTERVAL 22 DAY),8,30,7,1,1,0,0,2,5000000.00,333333.33,0.00,4666666.67,'proses',NOW(),NOW()),
(2,DATE_SUB(CURDATE(),INTERVAL 10 DAY),DATE_ADD(CURDATE(),INTERVAL 21 DAY),9,30,8,0,0,1,0,1,4500000.00,150000.00,0.00,4350000.00,'proses',NOW(),NOW()),
(3,DATE_SUB(CURDATE(),INTERVAL 10 DAY),DATE_ADD(CURDATE(),INTERVAL 21 DAY),9,30,8,0,0,0,1,1,7000000.00,233333.33,0.00,6766666.67,'proses',NOW(),NOW()),
(4,DATE_SUB(CURDATE(),INTERVAL 10 DAY),DATE_ADD(CURDATE(),INTERVAL 20 DAY),10,30,10,0,0,0,0,0,3800000.00,0.00,0.00,3800000.00,'proses',NOW(),NOW()),
(5,DATE_SUB(CURDATE(),INTERVAL 10 DAY),DATE_ADD(CURDATE(),INTERVAL 20 DAY),10,30,10,0,0,0,0,0,4800000.00,0.00,0.00,4800000.00,'proses',NOW(),NOW());

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================================
-- Login:
--   Admin    → admin@absensi.com  / password123
--   Karyawan → budi@absensi.com   / password123
--              siti@absensi.com   / password123
--              ahmad@absensi.com  / password123
--              dewi@absensi.com   / password123
--              rizky@absensi.com  / password123
-- ============================================================
