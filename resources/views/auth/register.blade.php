@extends('layouts.app')
@section('title', 'Daftar Anggota')
@section('content')
<div class="card" style="max-width:650px;margin:30px auto">
    <h1>Pendaftaran Akun Anggota</h1>
    <p class="meta">Khusus anggota PASCAL yang sudah resmi bergabung.</p>
    <form class="stack" method="post" action="{{ route('register.store') }}">
        @csrf
        <label>Nama lengkap</label><input name="nama" value="{{ old('nama') }}" maxlength="100" required>
        <label>Alamat email</label><input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password (minimal 8 karakter)</label><input type="password" name="password" minlength="8" required>
        <label>Ulangi password</label><input type="password" name="password_confirmation" minlength="8" required>
        <label>Angkatan</label><input name="angkatan" value="{{ old('angkatan') }}" required>
        <label>Status anggota</label>
        <select name="status" required><option value="">Pilih status</option><option>Siswa Aktif</option><option>Alumni</option></select>
        <label>Divisi</label>
        <select name="divisi_id" required><option value="">Pilih divisi</option>@foreach($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama_divisi }}</option>@endforeach</select>
        <button type="submit">Buat akun</button>
    </form>
    <p>Sudah memiliki akun? <a href="{{ route('login') }}">Login di sini</a>.</p>
</div>
@endsection
