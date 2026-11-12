@extends('layouts.app')
@section('title', 'Direktori Anggota')
@section('content')
<h1>Direktori Anggota</h1>
<p class="meta">Cari dan kenali anggota keluarga besar PASCAL.</p>
<form class="card inline-form" method="get" action="{{ route('members.directory') }}">
    <div><label for="search">Nama anggota</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama..."></div>
    <div><label for="angkatan">Angkatan</label><select id="angkatan" name="angkatan"><option value="">Semua angkatan</option>@foreach($angkatan as $year)<option value="{{ $year }}" @selected(request('angkatan') === $year)>{{ $year }}</option>@endforeach</select></div>
    <button type="submit">Cari</button>
    @if(request()->hasAny(['search', 'angkatan']))<a class="button secondary" href="{{ route('members.directory') }}">Reset</a>@endif
</form>
<div class="grid grid-3">
    @forelse($members as $member)
        <article class="card" style="text-align:center">
            @if($member->foto_profil)
                @php($image = file_exists(public_path('assets/uploads/'.$member->foto_profil)) ? asset('assets/uploads/'.$member->foto_profil) : \Illuminate\Support\Facades\Storage::url('uploads/'.$member->foto_profil))
                <img class="profile-photo" src="{{ $image }}" alt="{{ $member->nama }}">
            @else
                <div class="profile-photo" style="display:grid;place-items:center;margin:auto;font-size:40px;color:var(--green)">{{ mb_substr($member->nama, 0, 1) }}</div>
            @endif
            <h2>{{ $member->nama }}</h2>
            <p class="meta">NIA {{ $member->nia ?: '—' }} · Angkatan {{ $member->angkatan ?: '—' }}</p>
            <p><span class="badge">{{ $member->nama_divisi ?: 'Umum' }}</span></p>
            @auth<a class="button" href="{{ route('members.show', $member->id) }}">Lihat profil</a>@endauth
        </article>
    @empty
        <p>Tidak ada anggota yang cocok.</p>
    @endforelse
</div>
<div class="pagination">{{ $members->links() }}</div>
@endsection
