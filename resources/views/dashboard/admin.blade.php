@extends('layouts.app')
@section('content')
@php $rp = fn($n) => 'Rp'.number_format($n, 0, ',', '.'); $mx = max(1, $perJenis->max() ?? 1); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2"><h1 class="h3 mb-0">Ringkasan operasional</h1>
  <div class="d-flex gap-2"><a href="{{ route('admin.pengguna') }}" class="btn btn-sm btn-outline-dark">Kelola pengguna</a><a href="{{ route('admin.laporan',['hari'=>$hari]) }}" class="btn btn-sm btn-outline-dark">Cetak / ekspor laporan</a></div></div>
<div class="btn-group mb-3">
  @foreach([7=>'7 hari',30=>'30 hari',0=>'Semua'] as $d=>$l)
    <a href="?hari={{ $d }}" class="btn btn-sm {{ $hari==$d ? 'btn-dark' : 'btn-outline-dark' }}">{{ $l }}</a>
  @endforeach
</div>
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3"><div class="card hero p-3"><small>Nilai transaksi</small><div class="h4 mb-0">{{ $rp($totalNilai) }}</div></div></div>
  <div class="col-6 col-md-3"><div class="card p-3"><small>Sampah terkumpul</small><div class="h4 mb-0">{{ number_format($totalKg,1) }} kg</div></div></div>
  <div class="col-6 col-md-3"><div class="card p-3"><small>Nasabah terdaftar</small><div class="h4 mb-0">{{ $nasabah->count() }}</div></div></div>
  <div class="col-6 col-md-3"><div class="card p-3"><small>Penjemputan berjalan</small><div class="h4 mb-0">{{ $penjemputan->where('status','!=','selesai')->count() }}</div></div></div>
</div>
<div class="row g-3 mb-3">
  <div class="col-md-6"><div class="card p-3 h-100"><h2 class="h6">Sampah per jenis (kg)</h2>
    @forelse($perJenis as $n=>$kg)
      <div class="d-flex align-items-center gap-2 mb-2 small"><span style="width:100px">{{ $n }}</span>
        <div class="progress flex-grow-1" style="height:10px"><div class="progress-bar bg-dark" style="width:{{ $kg/$mx*100 }}%"></div></div><b>{{ number_format($kg,1) }}</b></div>
    @empty<p class="text-secondary mb-0">Belum ada setoran pada periode ini.</p>@endforelse
  </div></div>
  <div class="col-md-6"><div class="card p-3 h-100" id="form-setoran"><h2 class="h6">Catat setoran</h2>
    <form method="POST" action="{{ route('admin.setoran') }}">@csrf
      <input type="hidden" name="id_penjemputan" value="{{ request('penjemputan') }}">
      <select name="id_nasabah" class="form-select mb-2" required>@foreach($nasabah as $n)<option value="{{ $n->id_nasabah }}" @selected(request('nasabah')==$n->id_nasabah)>{{ $n->user->name }} - saldo {{ $rp($n->saldo) }}</option>@endforeach</select>
      <select name="id_jenis" id="jenis" class="form-select mb-2" required>@foreach($jenis as $j)<option value="{{ $j->id_jenis }}" data-h="{{ $j->harga_per_kg }}">{{ $j->nama_jenis }} - {{ $rp($j->harga_per_kg) }}/kg</option>@endforeach</select>
      <input name="berat" id="berat" type="number" step="0.1" min="0.1" class="form-control mb-2" placeholder="Berat (kg)" required>
      <div class="d-flex justify-content-between bg-light rounded p-2 mb-2"><span>Nilai setoran</span><b id="total">Rp0</b></div>
      <button class="btn btn-dark">Simpan setoran</button>
    </form>
  </div></div>
</div>

<div class="card p-3 mb-3"><h2 class="h6">Permintaan penjemputan</h2><div class="table-responsive"><table class="table table-sm align-middle mb-0">
  <thead><tr><th>Nasabah</th><th>Alamat</th><th>Tanggal</th><th>Status</th><th>Petugas</th></tr></thead><tbody>
  @forelse($penjemputan as $p)
    <tr><td>{{ $p->nasabah->user->name }}</td><td>{{ $p->alamat_penjemputan }}</td><td>{{ $p->tanggal }}</td>
      <td><span class="badge text-bg-{{ $p->status=='selesai' ? 'success' : ($p->status=='diajukan' ? 'warning' : 'info') }}">{{ ucfirst($p->status) }}</span></td>
      <td>@if($p->status=='diajukan')
        <form method="POST" action="{{ route('admin.tugaskan',$p) }}" class="d-flex gap-1">@csrf @method('PUT')
          <select name="id_petugas" class="form-select form-select-sm" required><option value="">Pilih petugas</option>@foreach($petugas as $x)<option value="{{ $x->id_petugas }}">{{ $x->user->name }}</option>@endforeach</select>
          <button class="btn btn-sm btn-dark">Tugaskan</button></form>
        @else {{ $p->petugas?->user->name }} @endif
        @if($p->status=='selesai' && $p->setoran->isEmpty())<a class="btn btn-sm btn-outline-dark ms-2" href="{{ route('dashboard',['hari'=>$hari,'nasabah'=>$p->id_nasabah,'penjemputan'=>$p->id_penjemputan]) }}#form-setoran">Catat setoran</a>@endif</td></tr>
  @empty<tr><td colspan="5" class="text-secondary">Belum ada permintaan penjemputan.</td></tr>@endforelse
</tbody></table></div></div>

<div class="card p-3 mb-3"><h2 class="h6">Laporan transaksi ({{ $hari ? $hari.' hari terakhir' : 'semua' }})</h2><div class="table-responsive"><table class="table table-sm mb-0">
  <thead><tr><th>Tanggal</th><th>Nasabah</th><th>Jenis</th><th class="text-end">Berat</th><th class="text-end">Nilai</th><th></th></tr></thead><tbody>
  @forelse($setoran as $s)
    <tr><td>{{ $s->tanggal_setoran }}</td><td>{{ $s->nasabah->user->name }}</td><td>{{ $s->jenis->nama_jenis }}</td><td class="text-end">{{ $s->berat }} kg</td><td class="text-end">{{ $rp($s->total_nilai) }}</td>
    <td class="text-end text-nowrap">
      <form method="POST" action="{{ route('admin.setoran.koreksi',$s) }}" class="d-inline-flex gap-1">@csrf @method('PUT')
        <input name="berat" type="number" step="0.1" min="0.1" value="{{ $s->berat }}" class="form-control form-control-sm" style="width:80px"><button class="btn btn-sm btn-outline-dark">Koreksi</button></form>
      <form method="POST" action="{{ route('admin.setoran.hapus',$s) }}" class="d-inline" onsubmit="return confirm('Hapus setoran ini? Saldo nasabah akan dikurangi.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
  @empty<tr><td colspan="6" class="text-secondary">Belum ada transaksi pada periode ini. Pilih periode lain.</td></tr>@endforelse
</tbody></table></div></div>

<div class="card p-3 mb-3"><h2 class="h6">Catat penarikan saldo</h2>
  <form method="POST" action="{{ route('admin.penarikan') }}" class="row g-2">@csrf
    <div class="col-md-4"><select name="id_nasabah" class="form-select" required>@foreach($nasabah as $n)<option value="{{ $n->id_nasabah }}">{{ $n->user->name }} - saldo {{ $rp($n->saldo) }}</option>@endforeach</select></div>
    <div class="col-md-3"><input name="jumlah" type="number" min="1" class="form-control" placeholder="Jumlah (Rp)" value="{{ old('jumlah') }}" required></div>
    <div class="col-md-3"><input name="catatan" class="form-control" placeholder="Catatan (opsional)"></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Catat penarikan</button></div>
  </form><small class="text-secondary mt-2">Uang diserahkan tunai di luar sistem. Sistem hanya mencatat dan mengurangi saldo.</small></div>

<div class="row g-3">
  <div class="col-md-6"><div class="card p-3 h-100"><h2 class="h6">Harga sampah</h2>
    @foreach($jenis as $j)
      <form method="POST" action="{{ route('admin.harga',$j) }}" class="d-flex gap-2 mb-2">@csrf @method('PUT')
        <span class="flex-grow-1 pt-1">{{ $j->nama_jenis }}</span>
        <input name="harga_per_kg" type="number" min="0" value="{{ (int)$j->harga_per_kg }}" class="form-control form-control-sm" style="width:110px">
        <button class="btn btn-sm btn-outline-dark">Simpan</button></form>
    @endforeach
    <form method="POST" action="{{ route('admin.jenis') }}" class="d-flex gap-2 mt-2">@csrf
      <input name="nama_jenis" class="form-control form-control-sm" placeholder="Jenis baru" required>
      <input name="harga_per_kg" type="number" min="0" class="form-control form-control-sm" style="width:110px" placeholder="Harga/kg" required>
      <button class="btn btn-sm btn-dark">Tambah</button></form>
  </div></div>
  <div class="col-md-6"><div class="card p-3 h-100"><h2 class="h6">Tambah akun petugas</h2>
    <form method="POST" action="{{ route('admin.petugas') }}">@csrf
      <input name="name" class="form-control form-control-sm mb-2" placeholder="Nama lengkap" required>
      <input name="email" type="email" class="form-control form-control-sm mb-2" placeholder="Email" required>
      <input name="password" type="password" class="form-control form-control-sm mb-2" placeholder="Kata sandi (min. 6 karakter)" required>
      <input name="nomor_telepon" class="form-control form-control-sm mb-2" placeholder="Nomor telepon" required>
      <button class="btn btn-sm btn-dark">Buat akun petugas</button></form>
  </div></div>
</div>
<script>
const j=document.getElementById('jenis'),b=document.getElementById('berat'),t=document.getElementById('total');
const up=()=>t.textContent='Rp'+Math.round((parseFloat(b.value)||0)*j.selectedOptions[0].dataset.h).toLocaleString('id-ID');
b.oninput=up;j.onchange=up;
</script>
@endsection
