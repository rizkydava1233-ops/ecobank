<?php
namespace App\Http\Controllers;
use App\Models\{JenisSampah, Nasabah, Penarikan, Penjemputan, Petugas, Setoran, User};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function setoran(Request $r)
    {
        $d = $r->validate([
            'id_nasabah' => 'required|exists:nasabah,id_nasabah',
            'id_jenis' => 'required|exists:jenis_sampah,id_jenis',
            'berat' => 'required|numeric|min:0.1',
            'id_penjemputan' => 'nullable|exists:penjemputan,id_penjemputan',
        ]);
        DB::transaction(function () use ($d) {
            $j = JenisSampah::findOrFail($d['id_jenis']);
            $total = $d['berat'] * $j->harga_per_kg;
            Setoran::create($d + ['harga_per_kg' => $j->harga_per_kg, 'total_nilai' => $total, 'tanggal_setoran' => now()->toDateString()]);
            Nasabah::where('id_nasabah', $d['id_nasabah'])->increment('saldo', $total);
        });
        return back()->with('ok', 'Setoran tersimpan, saldo nasabah diperbarui.');
    }

    public function tugaskan(Request $r, Penjemputan $penjemputan)
    {
        $d = $r->validate(['id_petugas' => 'required|exists:petugas,id_petugas']);
        $penjemputan->update($d + ['status' => 'ditugaskan']);
        return back()->with('ok', 'Petugas ditugaskan.');
    }

    public function petugas(Request $r) // akun petugas hanya dibuat admin
    {
        $d = $r->validate(['name' => 'required|max:100', 'email' => 'required|email|unique:users', 'password' => 'required|min:6', 'nomor_telepon' => 'required|max:20']);
        DB::transaction(function () use ($d) {
            $u = User::forceCreate(['name' => $d['name'], 'email' => $d['email'], 'password' => Hash::make($d['password']), 'role' => 'petugas']);
            Petugas::create(['id_user' => $u->id, 'nomor_telepon' => $d['nomor_telepon']]);
        });
        return back()->with('ok', 'Akun petugas dibuat.');
    }

    public function jenis(Request $r)
    {
        JenisSampah::create($r->validate(['nama_jenis' => 'required|max:60|unique:jenis_sampah', 'harga_per_kg' => 'required|numeric|min:0']));
        return back()->with('ok', 'Jenis sampah ditambahkan.');
    }

    public function harga(Request $r, JenisSampah $jenis)
    {
        $jenis->update($r->validate(['harga_per_kg' => 'required|numeric|min:0']));
        return back()->with('ok', 'Harga diperbarui. Setoran lama tidak berubah.');
    }

    // ---- koreksi setoran: saldo ikut disesuaikan, harga lama dipertahankan ----
    public function koreksi(Request $r, Setoran $setoran)
    {
        $d = $r->validate(['berat' => 'required|numeric|min:0.1']);
        return DB::transaction(function () use ($setoran, $d) {
            $baru = $d['berat'] * $setoran->harga_per_kg;
            $selisih = $baru - $setoran->total_nilai;
            $n = Nasabah::lockForUpdate()->findOrFail($setoran->id_nasabah);
            if ($n->saldo + $selisih < 0) {
                return back()->withErrors(['berat' => 'Koreksi ditolak: saldo nasabah akan negatif karena sebagian sudah ditarik.']);
            }
            $setoran->update(['berat' => $d['berat'], 'total_nilai' => $baru]);
            $n->increment('saldo', $selisih);
            return back()->with('ok', 'Setoran dikoreksi dan saldo disesuaikan.');
        });
    }

    public function hapusSetoran(Setoran $setoran)
    {
        return DB::transaction(function () use ($setoran) {
            $n = Nasabah::lockForUpdate()->findOrFail($setoran->id_nasabah);
            if ($n->saldo < $setoran->total_nilai) {
                return back()->withErrors(['berat' => 'Setoran tidak bisa dihapus: saldo nasabah tidak cukup karena sebagian sudah ditarik.']);
            }
            $n->decrement('saldo', $setoran->total_nilai);
            $setoran->delete();
            return back()->with('ok', 'Setoran dihapus dan saldo disesuaikan.');
        });
    }

    // ---- penarikan saldo: dicatat di sistem, uang diserahkan tunai ----
    public function penarikan(Request $r)
    {
        $d = $r->validate(['id_nasabah' => 'required|exists:nasabah,id_nasabah', 'jumlah' => 'required|numeric|min:1', 'catatan' => 'nullable|max:255']);
        return DB::transaction(function () use ($d) {
            $n = Nasabah::lockForUpdate()->findOrFail($d['id_nasabah']);
            if ($d['jumlah'] > $n->saldo) {
                return back()->withErrors(['jumlah' => 'Jumlah melebihi saldo nasabah (Rp'.number_format($n->saldo, 0, ',', '.').').'])->withInput();
            }
            Penarikan::create($d + ['tanggal_penarikan' => now()->toDateString()]);
            $n->decrement('saldo', $d['jumlah']);
            return back()->with('ok', 'Penarikan dicatat. Serahkan uang tunai ke nasabah.');
        });
    }

    // ---- kelola pengguna ----
    public function pengguna()
    {
        return view('dashboard.pengguna', ['nasabah' => Nasabah::with('user')->get(), 'petugas' => Petugas::with('user')->get()]);
    }

    private function simpanAkun($user, array $d): void
    {
        $user->forceFill(['name' => $d['name'], 'email' => $d['email']] + (!empty($d['password']) ? ['password' => Hash::make($d['password'])] : []))->save();
    }

    public function updateNasabah(Request $r, Nasabah $nasabah)
    {
        $d = $r->validate(['name' => 'required|max:100', 'email' => ['required', 'email', Rule::unique('users')->ignore($nasabah->id_user)],
            'nomor_telepon' => 'required|max:20', 'alamat' => 'required|max:255', 'password' => 'nullable|min:6']);
        DB::transaction(function () use ($d, $nasabah) {
            $this->simpanAkun($nasabah->user, $d);
            $nasabah->update(Arr::only($d, ['nomor_telepon', 'alamat']));
        });
        return back()->with('ok', 'Data nasabah diperbarui.');
    }

    public function hapusNasabah(Nasabah $nasabah)
    {
        if ($nasabah->saldo > 0 || $nasabah->setoran()->exists() || $nasabah->penjemputan()->exists() || $nasabah->penarikan()->exists()) {
            return back()->withErrors(['hapus' => 'Nasabah tidak bisa dihapus karena sudah punya saldo atau riwayat transaksi.']);
        }
        $nasabah->user->delete();
        return back()->with('ok', 'Nasabah dihapus.');
    }

    public function updatePetugas(Request $r, Petugas $petugas)
    {
        $d = $r->validate(['name' => 'required|max:100', 'email' => ['required', 'email', Rule::unique('users')->ignore($petugas->id_user)],
            'nomor_telepon' => 'required|max:20', 'password' => 'nullable|min:6']);
        DB::transaction(function () use ($d, $petugas) {
            $this->simpanAkun($petugas->user, $d);
            $petugas->update(['nomor_telepon' => $d['nomor_telepon']]);
        });
        return back()->with('ok', 'Data petugas diperbarui.');
    }

    public function hapusPetugas(Petugas $petugas)
    {
        DB::transaction(function () use ($petugas) { // tugas yang belum selesai kembali ke antrean
            Penjemputan::where('id_petugas', $petugas->id_petugas)->where('status', '!=', 'selesai')->update(['id_petugas' => null, 'status' => 'diajukan']);
            $petugas->user->delete();
        });
        return back()->with('ok', 'Petugas dihapus. Tugas yang belum selesai kembali ke antrean.');
    }

    // ---- laporan: cetak (simpan sebagai PDF dari peramban) dan CSV ----
    public function laporan(Request $r)
    {
        $hari = (int) $r->input('hari', 30);
        return view('dashboard.laporan', [
            'hari' => $hari,
            'setoran' => Setoran::with(['nasabah.user', 'jenis'])->periode($hari)->orderBy('tanggal_setoran')->get(),
            'penarikan' => Penarikan::with('nasabah.user')->periode($hari)->orderBy('tanggal_penarikan')->get(),
        ]);
    }

    public function csv(Request $r)
    {
        $rows = Setoran::with(['nasabah.user', 'jenis'])->periode((int) $r->input('hari', 30))->orderBy('tanggal_setoran')->get();
        return response()->streamDownload(function () use ($rows) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, ['Tanggal', 'Nasabah', 'Jenis', 'Berat (kg)', 'Harga/kg', 'Nilai']);
            foreach ($rows as $s) fputcsv($f, [$s->tanggal_setoran, $s->nasabah->user->name, $s->jenis->nama_jenis, $s->berat, $s->harga_per_kg, $s->total_nilai]);
            fclose($f);
        }, 'laporan-ecobank.csv', ['Content-Type' => 'text/csv']);
    }
}
