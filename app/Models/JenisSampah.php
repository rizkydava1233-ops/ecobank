<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    protected $table = 'jenis_sampah';
    protected $primaryKey = 'id_jenis';
    protected $fillable = ['nama_jenis','harga_per_kg'];

}
