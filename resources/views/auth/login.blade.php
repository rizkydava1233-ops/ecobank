@extends('layouts.app')
@section('content')
<div class="mx-auto" style="max-width:420px"><div class="card p-4">
  <h1 class="h4 text-center">Masuk ke EcoBank</h1>
  <p class="text-center text-secondary">Masuk dengan email dan kata sandi.</p>
  <form method="POST" action="{{ route('login') }}">@csrf
    <label class="form-label">Email</label>
    <input name="email" type="email" value="{{ old('email') }}" class="form-control mb-3" required autofocus>
    <label class="form-label">Kata sandi</label>
    <input name="password" type="password" class="form-control mb-3" required>
    <button class="btn btn-dark w-100">Masuk</button>
  </form>
  <p class="text-center text-secondary small mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
</div></div>
@endsection
