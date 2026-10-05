<?php
namespace Database\Seeders;
use App\Models\{JenisSampah, Nasabah, Petugas, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['Plastik PET', 3500], ['Kardus', 2000], ['Kertas HVS', 2500], ['Botol kaca', 800], ['Kaleng', 6000]] as [$n, $h]) {
            JenisSampah::firstOrCreate(['nama_jenis' => $n], ['harga_per_kg' => $h]);
        }
        // Akun admin dan petugas hanya dibuat di sini (database). GANTI kata sandi sebelum dipakai sungguhan.
        User::forceCreate(['name' => 'Admin Pengelola', 'email' => 'admin@ecobank.id', 'password' => Hash::make('admin123'), 'role' => 'admin']);
        $p = User::forceCreate(['name' => 'Joko', 'email' => 'joko@mail.com', 'password' => Hash::make('joko123'), 'role' => 'petugas']);
        Petugas::create(['id_user' => $p->id, 'nomor_telepon' => '081200000001']);
        $n = User::forceCreate(['name' => 'Siti Aminah', 'email' => 'siti@mail.com', 'password' => Hash::make('siti123'), 'role' => 'nasabah']);
        Nasabah::create(['id_user' => $n->id, 'alamat' => 'Jl. Melati 12, RT 03', 'nomor_telepon' => '081234567890']);
    }
}
