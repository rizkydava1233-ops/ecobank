@extends('layouts.app')
@section('content')
@php $rp = fn($n) => 'Rp'.number_format($n, 0, ',', '.'); @endphp
<div class="noprint d-flex flex-wrap gap-2 mb-3">
  <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">Kembali</a>
  @foreach([7=>'7 hari',30=>'30 hari',0=>'Semua'] as $d=>$l)<a href="?hari={{ $d }}" class="btn btn-sm {{ $hari==$d ? 'btn-dark' : 'btn-outline-dark' }}">{{ $l }}</a>@endforeach
  <button class="btn btn-sm btn-dark ms-auto" onclick="window.print()">Cetak / simpan sebagai PDF</button>
  <a href="{{ route('admin.laporan.csv',['hari'=>$hari]) }}" class="btn btn-sm btn-outline-dark">Unduh CSV</a>
</div>
<h1 class="h4 mb-0">Laporan transaksi EcoBank</h1>
<p class="text-secondary">Periode: {{ $hari ? $hari.' hari terakhir' : 'semua data' }}. Dicetak {{ now()->format('d-m-Y H:i') }}.</p>
<div class="row g-3 mb-3">
  <div class="col-4"><div class="card p-3"><small>Total setoran</small><div class="h5 mb-0">{{ $rp($setoran->sum('total_nilai')) }}</div></div></div>
  <div class="col-4"><div class="card p-3"><small>Total berat</small><div class="h5 mb-0">{{ number_format($setoran->sum('berat'),1) }} kg</div></div></div>
  <div class="col-4"><div class="card p-3"><small>Total penarikan</small><div class="h5 mb-0">{{ $rp($penarikan->sum('jumlah')) }}</div></div></div>
</div>
<h2 class="h6">Setoran</h2>
<table class="table table-sm"><thead><tr><th>Tanggal</th><th>Nasabah</th><th>Jenis</th><th class="text-end">Berat</th><th class="text-end">Harga/kg</th><th class="text-end">Nilai</th></tr></thead><tbody>
@forelse($setoran as $s)<tr><td>{{ $s->tanggal_setoran }}</td><td>{{ $s->nasabah->user->name }}</td><td>{{ $s->jenis->nama_jenis }}</td><td class="text-end">{{ $s->berat }} kg</td><td class="text-end">{{ $rp($s->harga_per_kg) }}</td><td class="text-end">{{ $rp($s->total_nilai) }}</td></tr>
@empty<tr><td colspan="6" class="text-secondary">Tidak ada setoran pada periode ini.</td></tr>@endforelse
</tbody></table>
<h2 class="h6 mt-4">Penarikan saldo</h2>
<table class="table table-sm"><thead><tr><th>Tanggal</th><th>Nasabah</th><th>Catatan</th><th class="text-end">Jumlah</th></tr></thead><tbody>
@forelse($penarikan as $w)<tr><td>{{ $w->tanggal_penarikan }}</td><td>{{ $w->nasabah->user->name }}</td><td>{{ $w->catatan }}</td><td class="text-end">{{ $rp($w->jumlah) }}</td></tr>
@empty<tr><td colspan="4" class="text-secondary">Tidak ada penarikan pada periode ini.</td></tr>@endforelse
</tbody></table>
@endsection
