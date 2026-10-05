<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    protected $table = 'nasabah';
    protected $primaryKey = 'id_nasabah';
    protected $fillable = ['id_user','alamat','nomor_telepon','saldo'];
    public function user() { return $this->belongsTo(User::class, 'id_user'); }
    public function setoran() { return $this->hasMany(Setoran::class, 'id_nasabah', 'id_nasabah'); }
    public function penarikan() { return $this->hasMany(Penarikan::class, 'id_nasabah', 'id_nasabah'); }
    public function penjemputan() { return $this->hasMany(Penjemputan::class, 'id_nasabah', 'id_nasabah'); }
}
