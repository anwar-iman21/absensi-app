<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id','aksi','model','model_id','deskripsi','ip_address','user_agent'];

    public function user() { return $this->belongsTo(User::class); }

    public static function catat(string $aksi, string $deskripsi, string $model = null, int $modelId = null): void
    {
        self::create([
            'user_id'    => auth()->id(),
            'aksi'       => $aksi,
            'model'      => $model,
            'model_id'   => $modelId,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
