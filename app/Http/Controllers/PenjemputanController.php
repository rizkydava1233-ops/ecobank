<?php
namespace App\Http\Controllers;
use App\Models\{Nasabah, Penjemputan, Petugas};
use Illuminate\Http\Request;

class PenjemputanController extends Controller
{
    public function store(Request $r) // nasabah
    {
        $d = $r->validate(['alamat_penjemputan' => 'required|max:255', 'tanggal' => 'required|date|after_or_equal:today', 'catatan' => 'nullable|max:255']);
        Nasabah::where('id_user', $r->user()->id)->firstOrFail()->penjemputan()->create($d + ['status' => 'diajukan']);
        return back()->with('ok', 'Penjemputan diajukan.');
    }

    public function status(Request $r, Penjemputan $penjemputan) // petugas
    {
        $p = Petugas::where('id_user', $r->user()->id)->firstOrFail();
        abort_unless($penjemputan->id_petugas == $p->id_petugas, 403);
        $next = ['ditugaskan' => 'dijemput', 'dijemput' => 'selesai'][$penjemputan->status] ?? null;
        abort_unless($next, 422);
        $penjemputan->update(['status' => $next]);
        return back()->with('ok', 'Status diperbarui.');
    }
}
