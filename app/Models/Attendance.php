<?php
// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id','tanggal','jam_masuk','jam_pulang',
        'status','terlambat','menit_terlambat','keterangan','device_scan',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'terlambat' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'hadir'  => '<span class="badge bg-success">Hadir</span>',
            'izin'   => '<span class="badge bg-warning text-dark">Izin</span>',
            'sakit'  => '<span class="badge bg-info">Sakit</span>',
            'alfa'   => '<span class="badge bg-danger">Alfa</span>',
            'cuti'   => '<span class="badge bg-secondary">Cuti</span>',
            default  => '<span class="badge bg-light text-dark">-</span>',
        };
    }
}
