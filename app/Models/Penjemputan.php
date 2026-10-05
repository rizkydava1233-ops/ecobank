<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Penjemputan extends Model
{
    protected $table = 'penjemputan';
    protected $primaryKey = 'id_penjemputan';
    protected $fillable = ['id_nasabah','id_petugas','alamat_penjemputan','tanggal','catatan','status'];
    public function nasabah() { return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah'); }
    public function setoran() { return $this->hasMany(Setoran::class, 'id_penjemputan', 'id_penjemputan'); }
    public function petugas() { return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas'); }
}
