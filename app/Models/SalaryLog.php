<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalaryLog extends Model
{
    protected $fillable = ['payroll_id','employee_id','tanggal_bayar','jumlah','metode','keterangan','dibuat_oleh'];
    protected $casts = ['tanggal_bayar' => 'date', 'jumlah' => 'decimal:2'];

    public function payroll()  { return $this->belongsTo(Payroll::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}
