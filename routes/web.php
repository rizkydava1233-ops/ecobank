<?php
use App\Http\Controllers\{AdminController, AuthController, DashboardController, PenjemputanController};
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::view('/daftar', 'auth.register')->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/setoran', [AdminController::class, 'setoran'])->name('setoran');
        Route::put('/penjemputan/{penjemputan}/tugaskan', [AdminController::class, 'tugaskan'])->name('tugaskan');
        Route::post('/petugas', [AdminController::class, 'petugas'])->name('petugas');
        Route::post('/jenis', [AdminController::class, 'jenis'])->name('jenis');
        Route::put('/jenis/{jenis}', [AdminController::class, 'harga'])->name('harga');
        Route::put('/setoran/{setoran}', [AdminController::class, 'koreksi'])->name('setoran.koreksi');
        Route::delete('/setoran/{setoran}', [AdminController::class, 'hapusSetoran'])->name('setoran.hapus');
        Route::post('/penarikan', [AdminController::class, 'penarikan'])->name('penarikan');
        Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
        Route::put('/nasabah/{nasabah}', [AdminController::class, 'updateNasabah'])->name('nasabah.update');
        Route::delete('/nasabah/{nasabah}', [AdminController::class, 'hapusNasabah'])->name('nasabah.hapus');
        Route::put('/petugas/{petugas}', [AdminController::class, 'updatePetugas'])->name('petugas.update');
        Route::delete('/petugas/{petugas}', [AdminController::class, 'hapusPetugas'])->name('petugas.hapus');
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::get('/laporan.csv', [AdminController::class, 'csv'])->name('laporan.csv');
    });
    Route::post('/penjemputan', [PenjemputanController::class, 'store'])->middleware('role:nasabah')->name('penjemputan.store');
    Route::put('/tugas/{penjemputan}', [PenjemputanController::class, 'status'])->middleware('role:petugas')->name('tugas.status');
});
