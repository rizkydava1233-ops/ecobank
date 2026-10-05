<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    protected $table = 'setoran';
    protected $primaryKey = 'id_setoran';
    protected $fillable = ['id_nasabah','id_jenis','id_penjemputan','berat','harga_per_kg','total_nilai','tanggal_setoran'];
    public function nasabah() { return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah'); }
    public function scopePeriode($q, int $hari) {
        return $q->when($hari > 0, fn ($q) => $q->where('tanggal_setoran', '>=', now()->subDays($hari)->toDateString()));
    }
    public function jenis() { return $this->belongsTo(JenisSampah::class, 'id_jenis', 'id_jenis'); }
}
