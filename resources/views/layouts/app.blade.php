<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PASCAL Caldera - Pecinta Alam Sebelas Caldera')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="navbar">
    <a class="brand" href="{{ route('home') }}">
        <img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo PASCAL">
        <span><strong>PASCAL</strong><small>PECINTA ALAM SEBELAS CALDERA</small></span>
    </a>
    <nav>
        <a href="{{ route('home') }}">Beranda</a>
        <a href="{{ route('gallery') }}">Galeri</a>
        <a href="{{ route('journal') }}">Jurnal</a>
        <a href="{{ route('applications.create') }}">Pendaftaran</a>
        <a href="{{ route('members.directory') }}">Anggota</a>
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Admin</a>
            @elseif(auth()->user()->role === 'pengurus')
                <a href="{{ route('officers.applications') }}">Penerimaan</a>
            @endif
            <a href="{{ route('members.profile') }}">Profil</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-button">Keluar</button></form>
        @else
            <a class="nav-cta" href="{{ route('login') }}">Login</a>
        @endauth
    </nav>
</header>

<main class="page">
    @if(session('success'))
        <div class="notice success">{{ session('success') }}</div>
    @endif
    @if(session('temporary_password'))
        <div class="notice warning"><strong>Password sementara:</strong> {{ session('temporary_password') }}<br>Berikan langsung kepada anggota dan minta mereka menggantinya setelah masuk.</div>
    @endif
    @if($errors->any())
        <div class="notice error"><strong>Periksa kembali formulir:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
<footer class="footer">Salam Rimba · PASCAL — Pecinta Alam Sebelas Caldera, SMAN 11 Bekasi</footer>
</body>
</html>
