@extends('layouts.app')
@section('content')
@php $rp = fn($n) => 'Rp'.number_format($n, 0, ',', '.'); $steps = ['diajukan','ditugaskan','dijemput','selesai']; $act = $penjemputan->firstWhere('status','!=','selesai') ?? $penjemputan->first(); $kg = $setoran->sum('berat'); @endphp
<h1 class="h3 mb-3">Dasbor Nasabah</h1>
<div class="row g-3 mb-3">
  <div class="col-md-5">
    <div class="card hero p-3 mb-3"><small>Saldo Anda</small><div class="h3">{{ $rp($n->saldo) }}</div>
      <small>{{ number_format($kg*10,0,',','.') }} PTS - {{ number_format($kg,1) }} kg disetor</small></div>
    <div class="card p-3"><h2 class="h6">Data akun nasabah</h2>
      <small class="text-secondary">Nama lengkap</small><div class="mb-2">{{ auth()->user()->name }}</div>
      <small class="text-secondary">Nomor telepon</small><div class="mb-2">{{ $n->nomor_telepon }}</div>
      <small class="text-secondary">Alamat penjemputan</small><div>{{ $n->alamat }}</div></div>
  </div>
  <div class="col-md-7">
    <div class="card p-3 mb-3"><h2 class="h6">Riwayat setoran sampah</h2><div class="table-responsive"><table class="table table-sm mb-0">
      <thead><tr><th>Tanggal</th><th>Jenis</th><th class="text-end">Berat</th><th class="text-end">Nilai</th></tr></thead><tbody>
      @forelse($setoran as $s)<tr><td>{{ $s->tanggal_setoran }}</td><td>{{ $s->jenis->nama_jenis }}</td><td class="text-end">{{ $s->berat }} kg</td><td class="text-end">{{ $rp($s->total_nilai) }}</td></tr>
      @empty<tr><td colspan="4" class="text-secondary">Belum ada setoran. Bawa sampahmu ke bank sampah atau ajukan penjemputan.</td></tr>@endforelse
    </tbody></table></div></div>
    <div class="card p-3"><h2 class="h6">Status penjemputan aktif</h2>
      @if($act)
        <div class="d-flex gap-1 mb-2">@foreach($steps as $i=>$s)<div class="flex-fill small pt-1 {{ $i<=array_search($act->status,$steps) ? 'fw-bold border-top border-3 border-dark' : 'text-secondary border-top border-3' }}">{{ ucfirst($s) }}</div>@endforeach</div>
        <div class="small">{{ $act->tanggal }} - {{ $act->alamat_penjemputan }}<br>Petugas: {{ $act->petugas?->user->name ?? 'Belum ditugaskan' }}</div>
      @else<p class="text-secondary mb-0">Belum ada permintaan. Isi formulir di bawah.</p>@endif
    </div>
  </div>
</div>
<div class="card p-3 mb-3"><h2 class="h6">Riwayat penarikan saldo</h2><div class="table-responsive"><table class="table table-sm mb-0">
  <thead><tr><th>Tanggal</th><th>Catatan</th><th class="text-end">Jumlah</th></tr></thead><tbody>
  @forelse($penarikan as $w)<tr><td>{{ $w->tanggal_penarikan }}</td><td>{{ $w->catatan }}</td><td class="text-end">{{ $rp($w->jumlah) }}</td></tr>
  @empty<tr><td colspan="3" class="text-secondary">Belum ada penarikan. Hubungi pengurus untuk menarik saldo.</td></tr>@endforelse
</tbody></table></div></div>
<div class="card p-3"><h2 class="h6">Formulir pengajuan penjemputan</h2>
  <form method="POST" action="{{ route('penjemputan.store') }}" class="row g-2">@csrf
    <div class="col-md-5"><input name="alamat_penjemputan" class="form-control" value="{{ old('alamat_penjemputan', $n->alamat) }}" placeholder="Alamat penjemputan" required></div>
    <div class="col-md-3"><input name="tanggal" type="date" min="{{ now()->toDateString() }}" class="form-control" value="{{ old('tanggal') }}" required></div>
    <div class="col-md-4"><input name="catatan" class="form-control" placeholder="Catatan (opsional)" value="{{ old('catatan') }}"></div>
    <div class="col-12"><button class="btn btn-dark">Ajukan penjemputan</button></div>
  </form></div>
@endsection
