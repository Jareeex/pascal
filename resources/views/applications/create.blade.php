@extends('layouts.app')
@section('title', 'Pendaftaran Calon Anggota')
@section('content')
<div class="card" style="max-width:760px;margin:20px auto">
    <h1>Pendaftaran Calon Anggota</h1>
    @if(!$open)
        <div class="notice warning">Pendaftaran sedang ditutup. Silakan kembali lagi nanti.</div>
    @else
        <p class="meta">Lengkapi data di bawah ini untuk mendaftar menjadi anggota PASCAL.</p>
        <form class="stack" method="post" action="{{ route('applications.store') }}">
            @csrf
            <label>Nama lengkap</label><input name="nama_lengkap" maxlength="100" value="{{ old('nama_lengkap') }}" required>
            <label>Nama panggilan</label><input name="nama_panggilan" maxlength="50" value="{{ old('nama_panggilan') }}">
            <label>Email</label><input type="email" name="email" maxlength="100" value="{{ old('email') }}" required>
            <label>Nomor telepon / WhatsApp</label><input name="nomor_telepon" maxlength="20" value="{{ old('nomor_telepon') }}" required>
            <div class="grid">
                <div><label>Tempat lahir</label><input name="tempat_lahir" maxlength="50"></div>
                <div><label>Tanggal lahir</label><input type="date" name="tanggal_lahir"></div>
            </div>
            <label>Jenis kelamin</label><select name="jenis_kelamin" required><option value="L">Laki-laki</option><option value="P">Perempuan</option></select>
            <label>Alamat</label><textarea name="alamat"></textarea>
            <label>Divisi minat</label>
            <select name="divisi_minat"><option value="">Pilih divisi</option>@foreach($divisions as $division)<option value="{{ $division->nama_divisi }}">{{ $division->nama_divisi }}</option>@endforeach</select>
            <label>Alasan ingin bergabung</label><textarea name="alasan_bergabung"></textarea>
            <button type="submit">Kirim pendaftaran</button>
        </form>
    @endif
</div>
@endsection
