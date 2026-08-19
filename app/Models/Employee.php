<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','employee_id','nama_lengkap','foto','jabatan','divisi',
        'no_hp','email','alamat','tanggal_lahir','jenis_kelamin',
        'tanggal_masuk','status_kerja','gaji_pokok','shift_id',
        'barcode','status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'gaji_pokok'    => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    // Hitung total hadir bulan ini
    public function totalHadirBulanIni(): int
    {
        return $this->attendances()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'hadir')
            ->count();
    }

    // Hitung hari tidak masuk (izin+sakit+alfa+cuti) bulan ini
    public function totalTidakMasukBulanIni(): int
    {
        return $this->attendances()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->whereIn('status', ['izin','sakit','alfa','cuti'])
            ->count();
    }

    // Estimasi gajian berikutnya berdasarkan payroll aktif
    public function estimasiGajian()
    {
        $payroll = $this->payrolls()->where('status', 'proses')->latest()->first();
        return $payroll ? $payroll->estimasi_gajian : null;
    }

    // Sisa hari menuju gajian
    public function sisaHariGajian(): int
    {
        $payroll = $this->payrolls()->where('status', 'proses')->latest()->first();
        if (!$payroll) return 0;
        $hadir = $this->attendances()
            ->where('tanggal', '>=', $payroll->periode_mulai)
            ->where('status', 'hadir')
            ->count();
        return max(0, $payroll->target_hari_kerja - $hadir);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/foto-karyawan/' . $this->foto)
            : asset('images/default-avatar.png');
    }
}
