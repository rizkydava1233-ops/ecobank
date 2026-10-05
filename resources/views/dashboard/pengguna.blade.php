@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Kelola pengguna</h1><a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">Kembali ke dasbor</a></div>
<h2 class="h5">Nasabah</h2>
@forelse($nasabah as $n)
<div class="card p-3 mb-2">
  <form method="POST" action="{{ route('admin.nasabah.update',$n) }}" class="row g-2">@csrf @method('PUT')
    <div class="col-md-3"><input name="name" value="{{ $n->user->name }}" class="form-control form-control-sm" required></div>
    <div class="col-md-3"><input name="email" type="email" value="{{ $n->user->email }}" class="form-control form-control-sm" required></div>
    <div class="col-md-2"><input name="nomor_telepon" value="{{ $n->nomor_telepon }}" class="form-control form-control-sm" required></div>
    <div class="col-md-4"><input name="alamat" value="{{ $n->alamat }}" class="form-control form-control-sm" required></div>
    <div class="col-md-5"><input name="password" type="password" class="form-control form-control-sm" placeholder="Kata sandi baru (kosongkan jika tidak diubah)"></div>
    <div class="col-md-7 d-flex align-items-center gap-2"><span class="small text-secondary">Saldo Rp{{ number_format($n->saldo,0,',','.') }}</span><button class="btn btn-sm btn-dark ms-auto">Simpan</button></div>
  </form>
  <form method="POST" action="{{ route('admin.nasabah.hapus',$n) }}" class="mt-2" onsubmit="return confirm('Hapus nasabah ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus nasabah</button></form>
</div>
@empty<p class="text-secondary">Belum ada nasabah.</p>@endforelse

<h2 class="h5 mt-4">Petugas</h2>
@forelse($petugas as $p)
<div class="card p-3 mb-2">
  <form method="POST" action="{{ route('admin.petugas.update',$p) }}" class="row g-2">@csrf @method('PUT')
    <div class="col-md-3"><input name="name" value="{{ $p->user->name }}" class="form-control form-control-sm" required></div>
    <div class="col-md-3"><input name="email" type="email" value="{{ $p->user->email }}" class="form-control form-control-sm" required></div>
    <div class="col-md-2"><input name="nomor_telepon" value="{{ $p->nomor_telepon }}" class="form-control form-control-sm" required></div>
    <div class="col-md-4"><input name="password" type="password" class="form-control form-control-sm" placeholder="Kata sandi baru (opsional)"></div>
    <div class="col-12 text-end"><button class="btn btn-sm btn-dark">Simpan</button></div>
  </form>
  <form method="POST" action="{{ route('admin.petugas.hapus',$p) }}" onsubmit="return confirm('Hapus petugas ini? Tugas yang belum selesai kembali ke antrean.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus petugas</button></form>
</div>
@empty<p class="text-secondary">Belum ada petugas. Tambahkan dari dasbor admin.</p>@endforelse
@endsection
