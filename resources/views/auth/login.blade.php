@extends('layouts.app')
@section('title', 'Login Anggota')
@section('content')
<div class="card" style="max-width:460px;margin:50px auto">
    <h1>Login Anggota</h1>
    <p class="meta">Gunakan alamat email atau NIA dan kata sandi Anda.</p>
    <form class="stack" method="post" action="{{ route('login.store') }}">
        @csrf
        <label for="login_input">Email / NIA</label>
        <input id="login_input" name="login_input" value="{{ old('login_input') }}" required autofocus>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
        <label style="font-weight:400"><input type="checkbox" name="remember" value="1" style="width:auto"> Ingat saya</label>
        <button type="submit">Masuk</button>
    </form>
    <p>Belum punya akun anggota? <a href="{{ route('register') }}">Daftar akun</a></p>
</div>
@endsection
