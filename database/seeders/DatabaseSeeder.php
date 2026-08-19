<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Shifts
        DB::table('shifts')->insert([
            ['nama_shift'=>'Shift Pagi', 'jam_masuk'=>'07:00:00','jam_pulang'=>'15:00:00','toleransi_menit'=>15,'keterangan'=>'Shift pagi hari','created_at'=>now(),'updated_at'=>now()],
            ['nama_shift'=>'Shift Siang','jam_masuk'=>'12:00:00','jam_pulang'=>'20:00:00','toleransi_menit'=>15,'keterangan'=>'Shift siang hari','created_at'=>now(),'updated_at'=>now()],
            ['nama_shift'=>'Shift Malam','jam_masuk'=>'20:00:00','jam_pulang'=>'04:00:00','toleransi_menit'=>15,'keterangan'=>'Shift malam hari','created_at'=>now(),'updated_at'=>now()],
        ]);

        // Users
        $users = [
            ['name'=>'Administrator', 'email'=>'admin@absensi.com',  'password'=>Hash::make('password123'),'role'=>'admin'],
            ['name'=>'Budi Santoso',  'email'=>'budi@absensi.com',   'password'=>Hash::make('password123'),'role'=>'karyawan'],
            ['name'=>'Siti Rahayu',   'email'=>'siti@absensi.com',   'password'=>Hash::make('password123'),'role'=>'karyawan'],
            ['name'=>'Ahmad Fauzi',   'email'=>'ahmad@absensi.com',  'password'=>Hash::make('password123'),'role'=>'karyawan'],
            ['name'=>'Dewi Kusuma',   'email'=>'dewi@absensi.com',   'password'=>Hash::make('password123'),'role'=>'karyawan'],
            ['name'=>'Rizky Pratama', 'email'=>'rizky@absensi.com',  'password'=>Hash::make('password123'),'role'=>'karyawan'],
        ];
        foreach ($users as &$u) { $u['created_at'] = now(); $u['updated_at'] = now(); }
        DB::table('users')->insert($users);

        // Employees
        $employees = [
            [2,'EMP001','Budi Santoso', 'Staff IT',       'IT',         '081234567890','budi@absensi.com', 'Jl. Merdeka No.1','1990-05-15','Laki-laki', '2022-01-03','Tetap',    5000000,1,'EMP001-ABC123'],
            [3,'EMP002','Siti Rahayu',  'Staff Keuangan', 'Keuangan',   '081234567891','siti@absensi.com', 'Jl. Sudirman No.2','1992-08-20','Perempuan', '2022-02-01','Tetap',    4500000,1,'EMP002-DEF456'],
            [4,'EMP003','Ahmad Fauzi',  'Supervisor',     'Operasional','081234567892','ahmad@absensi.com','Jl. Gatot No.3',  '1988-12-10','Laki-laki', '2021-06-15','Tetap',    7000000,2,'EMP003-GHI789'],
            [5,'EMP004','Dewi Kusuma',  'Staff HRD',      'HRD',        '081234567893','dewi@absensi.com', 'Jl. Ahmad Yani 4','1995-03-25','Perempuan', '2023-03-01','Kontrak',  3800000,1,'EMP004-JKL012'],
            [6,'EMP005','Rizky Pratama','Staff Marketing','Marketing',  '081234567894','rizky@absensi.com','Jl. Diponegoro 5','1993-07-07','Laki-laki', '2022-09-01','Tetap',    4800000,3,'EMP005-MNO345'],
        ];
        foreach ($employees as $e) {
            DB::table('employees')->insert([
                'user_id'=>$e[0],'employee_id'=>$e[1],'nama_lengkap'=>$e[2],
                'jabatan'=>$e[3],'divisi'=>$e[4],'no_hp'=>$e[5],'email'=>$e[6],
                'alamat'=>$e[7],'tanggal_lahir'=>$e[8],'jenis_kelamin'=>$e[9],
                'tanggal_masuk'=>$e[10],'status_kerja'=>$e[11],'gaji_pokok'=>$e[12],
                'shift_id'=>$e[13],'barcode'=>$e[14],'status_aktif'=>'aktif',
                'created_at'=>now(),'updated_at'=>now(),
            ]);
        }

        // Absensi contoh 10 hari terakhir untuk EMP001
        $empId = DB::table('employees')->where('employee_id','EMP001')->value('id');
        for ($i = 10; $i >= 1; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $status = $i === 7 ? 'izin' : ($i === 2 ? 'sakit' : 'hadir');
            DB::table('attendances')->insert([
                'employee_id' => $empId,
                'tanggal'     => $tgl->toDateString(),
                'jam_masuk'   => $status === 'hadir' ? '07:05:00' : null,
                'jam_pulang'  => $status === 'hadir' ? '15:00:00' : null,
                'status'      => $status,
                'terlambat'   => 0,
                'menit_terlambat' => 0,
                'device_scan' => 'Seeder',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Payroll awal untuk semua karyawan
        $emps = DB::table('employees')->get();
        foreach ($emps as $emp) {
            DB::table('payrolls')->insert([
                'employee_id'       => $emp->id,
                'periode_mulai'     => Carbon::today()->subDays(10),
                'estimasi_gajian'   => Carbon::today()->addDays(22),
                'target_hari_kerja' => 30,
                'gaji_pokok'        => $emp->gaji_pokok,
                'total_gaji'        => $emp->gaji_pokok,
                'status'            => 'proses',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
