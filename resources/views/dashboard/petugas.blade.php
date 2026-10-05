@extends('layouts.app')
@section('content')
@php $next = ['ditugaskan'=>'Tandai dijemput','dijemput'=>'Tandai selesai']; @endphp
<h1 class="h3 mb-3">Tugas penjemputan</h1>
<div class="row g-3">
  @forelse($tugas as $t)
    <div class="col-md-6"><div class="card p-3">
      <h2 class="h6">{{ $t->nasabah->user->name }}</h2>
      <div class="small mb-1">{{ $t->alamat_penjemputan }}</div>
      <div class="small text-secondary mb-2">{{ $t->tanggal }} @if($t->catatan)- {{ $t->catatan }}@endif</div>
      <div class="mb-2"><span class="badge text-bg-{{ $t->status=='selesai' ? 'success' : 'info' }}">{{ ucfirst($t->status) }}</span></div>
      @isset($next[$t->status])
        <form method="POST" action="{{ route('tugas.status',$t) }}">@csrf @method('PUT')<button class="btn btn-dark btn-sm">{{ $next[$t->status] }}</button></form>
      @else<small class="text-secondary">Menunggu pencatatan setoran oleh admin</small>@endisset
    </div></div>
  @empty
    <div class="col-12"><div class="card p-3 text-secondary">Belum ada tugas untukmu. Admin akan menugaskan permintaan baru.</div></div>
  @endforelse
</div>
@endsection
