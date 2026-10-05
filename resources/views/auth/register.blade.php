@extends('layouts.app')
@section('content')
<div class="mx-auto" style="max-width:420px"><div class="card p-4">
  <h1 class="h4 text-center">Daftar sebagai nasabah</h1>
  <p class="text-center text-secondary">Isi data berikut untuk membuat akun.</p>
  <form method="POST" action="{{ route('register') }}">@csrf
    @foreach(['name'=>['Nama lengkap','text'],'email'=>['Email','email'],'password'=>['Kata sandi (min. 6 karakter)','password'],'nomor_telepon'=>['Nomor telepon','tel'],'alamat'=>['Alamat penjemputan','text']] as $f=>[$l,$t])
      <label class="form-label">{{ $l }}</label>
      <input name="{{ $f }}" type="{{ $t }}" value="{{ $t=='password' ? '' : old($f) }}" class="form-control mb-3" required>
    @endforeach
    <button class="btn btn-dark w-100">Daftar</button>
  </form>
  <p class="text-center text-secondary small mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</div></div>
@endsection
