<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    protected $table = 'penarikan';
    protected $primaryKey = 'id_penarikan';
    protected $fillable = ['id_nasabah', 'jumlah', 'tanggal_penarikan', 'catatan'];
    public function nasabah() { return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah'); }
    public function scopePeriode($q, int $hari) {
        return $q->when($hari > 0, fn ($q) => $q->where('tanggal_penarikan', '>=', now()->subDays($hari)->toDateString()));
    }
}
