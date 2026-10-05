<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', fn (Blueprint $t) => $t->enum('role', ['admin', 'nasabah', 'petugas'])->default('nasabah'));
        Schema::create('nasabah', function (Blueprint $t) {
            $t->id('id_nasabah');
            $t->foreignId('id_user')->unique()->constrained('users')->cascadeOnDelete();
            $t->string('alamat');
            $t->string('nomor_telepon', 20);
            $t->decimal('saldo', 12, 2)->default(0);
            $t->timestamps();
        });
        Schema::create('petugas', function (Blueprint $t) {
            $t->id('id_petugas');
            $t->foreignId('id_user')->unique()->constrained('users')->cascadeOnDelete();
            $t->string('nomor_telepon', 20);
            $t->timestamps();
        });
        Schema::create('jenis_sampah', function (Blueprint $t) {
            $t->id('id_jenis');
            $t->string('nama_jenis', 60)->unique();
            $t->decimal('harga_per_kg', 10, 2);
            $t->timestamps();
        });
        Schema::create('setoran', function (Blueprint $t) {
            $t->id('id_setoran');
            $t->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah');
            $t->foreignId('id_jenis')->constrained('jenis_sampah', 'id_jenis');
            $t->decimal('berat', 8, 2);
            $t->decimal('harga_per_kg', 10, 2); // salinan harga saat transaksi
            $t->decimal('total_nilai', 12, 2);
            $t->date('tanggal_setoran');
            $t->timestamps();
        });
        Schema::create('penjemputan', function (Blueprint $t) {
            $t->id('id_penjemputan');
            $t->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah');
            $t->foreignId('id_petugas')->nullable()->constrained('petugas', 'id_petugas')->nullOnDelete();
            $t->string('alamat_penjemputan');
            $t->date('tanggal');
            $t->string('catatan')->nullable();
            $t->enum('status', ['diajukan', 'ditugaskan', 'dijemput', 'selesai'])->default('diajukan');
            $t->timestamps();
        });
    }
    public function down(): void {
        foreach (['penjemputan', 'setoran', 'jenis_sampah', 'petugas', 'nasabah'] as $n) Schema::dropIfExists($n);
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
