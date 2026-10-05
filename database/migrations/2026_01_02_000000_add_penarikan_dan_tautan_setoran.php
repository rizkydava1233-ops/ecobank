<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penarikan', function (Blueprint $t) {
            $t->id('id_penarikan');
            $t->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah');
            $t->decimal('jumlah', 12, 2);
            $t->date('tanggal_penarikan');
            $t->string('catatan')->nullable();
            $t->timestamps();
        });
        // menautkan setoran ke penjemputan asalnya
        Schema::table('setoran', fn (Blueprint $t) => $t->foreignId('id_penjemputan')->nullable()->after('id_jenis')
            ->constrained('penjemputan', 'id_penjemputan')->nullOnDelete());
    }
    public function down(): void {
        Schema::table('setoran', fn (Blueprint $t) => $t->dropConstrainedForeignId('id_penjemputan'));
        Schema::dropIfExists('penarikan');
    }
};
