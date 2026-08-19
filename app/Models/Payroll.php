<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id','periode_mulai','periode_akhir','estimasi_gajian',
        'total_hari_kerja','target_hari_kerja','total_hadir','total_izin',
        'total_sakit','total_alfa','total_cuti','hari_mundur',
        'gaji_pokok','potongan','bonus','total_gaji','status','catatan',
    ];

    protected $casts = [
        'periode_mulai'   => 'date',
        'periode_akhir'   => 'date',
        'estimasi_gajian' => 'date',
        'gaji_pokok'      => 'decimal:2',
        'potongan'        => 'decimal:2',
        'bonus'           => 'decimal:2',
        'total_gaji'      => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryLogs()
    {
        return $this->hasMany(SalaryLog::class);
    }

    // Hitung ulang gaji berdasarkan absensi
    public function hitungGaji(): void
    {
        $emp = $this->employee;
        $attendances = Attendance::where('employee_id', $this->employee_id)
            ->where('tanggal', '>=', $this->periode_mulai)
            ->get();

        $this->total_hadir = $attendances->where('status','hadir')->count();
        $this->total_izin  = $attendances->where('status','izin')->count();
        $this->total_sakit = $attendances->where('status','sakit')->count();
        $this->total_alfa  = $attendances->where('status','alfa')->count();
        $this->total_cuti  = $attendances->where('status','cuti')->count();
        $this->total_hari_kerja = $attendances->count();

        $tidakMasuk = $this->total_izin + $this->total_sakit + $this->total_alfa + $this->total_cuti;
        $this->hari_mundur    = $tidakMasuk;
        $this->estimasi_gajian = \Carbon\Carbon::parse($this->periode_mulai)
            ->addDays($this->target_hari_kerja + $tidakMasuk);

        // Potongan proporsional hari tidak masuk
        $perHari = $emp->gaji_pokok / $this->target_hari_kerja;
        $this->potongan   = round($perHari * $tidakMasuk, 2);
        $this->total_gaji = round($emp->gaji_pokok - $this->potongan + $this->bonus, 2);

        $this->save();
    }
}
