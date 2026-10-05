<?php
namespace App\Http\Controllers;
use App\Models\{JenisSampah, Nasabah, Penjemputan, Petugas, Setoran};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user();
        if ($u->role === 'admin') {
            $hari = (int) $r->input('hari', 30);
            $setoran = Setoran::with(['nasabah.user', 'jenis'])
                ->when($hari > 0, fn ($q) => $q->where('tanggal_setoran', '>=', now()->subDays($hari)->toDateString()))
                ->latest('tanggal_setoran')->latest('id_setoran')->get();
            return view('dashboard.admin', [
                'hari' => $hari, 'setoran' => $setoran,
                'totalKg' => $setoran->sum('berat'), 'totalNilai' => $setoran->sum('total_nilai'),
                'perJenis' => $setoran->groupBy('jenis.nama_jenis')->map->sum('berat'),
                'nasabah' => Nasabah::with('user')->get(), 'petugas' => Petugas::with('user')->get(),
                'jenis' => JenisSampah::all(),
                'penjemputan' => Penjemputan::with(['nasabah.user', 'petugas.user', 'setoran'])->latest('id_penjemputan')->get(),
            ]);
        }
        if ($u->role === 'nasabah') {
            $n = Nasabah::where('id_user', $u->id)->firstOrFail();
            return view('dashboard.nasabah', [
                'n' => $n,
                'setoran' => $n->setoran()->with('jenis')->latest('tanggal_setoran')->get(),
                'penjemputan' => $n->penjemputan()->latest('id_penjemputan')->get(),
                'penarikan' => $n->penarikan()->latest('id_penarikan')->get(),
            ]);
        }
        $p = Petugas::where('id_user', $u->id)->firstOrFail();
        return view('dashboard.petugas', ['tugas' => $p->penjemputan()->with('nasabah.user')->latest('tanggal')->get()]);
    }
}
