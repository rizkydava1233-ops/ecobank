<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>EcoBank - Bank Sampah Digital</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f4f4f3;color:#111}.card{border-color:#e2e2e0}.hero{background:#111;color:#fff}.hero .btn{background:#fff;color:#111}@media print{nav,.noprint{display:none!important}body{background:#fff}}</style>
</head>
<body>
<nav class="navbar bg-white border-bottom mb-4"><div class="container">
  <span class="navbar-brand fw-bold">EcoBank</span>
  @auth
  <div class="d-flex align-items-center gap-3">
    <span>{{ auth()->user()->name }} <span class="badge text-bg-secondary">{{ ucfirst(auth()->user()->role) }}</span></span>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-dark">Keluar</button></form>
  </div>
  @endauth
</div></nav>
<main class="container pb-5">
  @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
  @yield('content')
</main>
</body>
</html>
