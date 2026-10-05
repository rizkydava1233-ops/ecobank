<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    protected $fillable = ['id_user','nomor_telepon'];
    public function user() { return $this->belongsTo(User::class, 'id_user'); }
    public function penjemputan() { return $this->hasMany(Penjemputan::class, 'id_petugas', 'id_petugas'); }
}
