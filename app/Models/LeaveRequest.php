<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id','jenis','tanggal_mulai','tanggal_akhir',
        'jumlah_hari','keterangan','file_bukti','status','approved_by','catatan_admin',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning text-dark">Menunggu</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'ditolak'   => '<span class="badge bg-danger">Ditolak</span>',
            default     => '<span class="badge bg-secondary">-</span>',
        };
    }
}
