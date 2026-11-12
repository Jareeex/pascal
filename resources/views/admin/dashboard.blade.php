@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<h1>Dashboard Admin</h1>
<p>Selamat datang, {{ auth()->user()->nama }}.</p>
<div class="grid">
    @foreach([['Anggota', $stats['members'], route('admin.members')], ['Pendaftar menunggu', $stats['pending'], route('admin.applications')], ['Berita', $stats['news'], route('admin.news')], ['Agenda', $stats['events'], route('admin.events')], ['Jurnal', $stats['stories'], route('journal')]] as [$label, $count, $url])
        <a class="card" style="text-align:center;text-decoration:none" href="{{ $url }}"><strong style="font-size:34px;color:var(--green)">{{ $count }}</strong><div>{{ $label }}</div></a>
    @endforeach
</div>
<div class="actions">
    <a class="button" href="{{ route('admin.members') }}">Kelola anggota</a>
    <a class="button" href="{{ route('admin.news') }}">Kelola berita</a>
    <a class="button" href="{{ route('admin.events') }}">Kelola agenda</a>
    <a class="button" href="{{ route('admin.applications') }}">Penerimaan anggota</a>
</div>
@endsection
