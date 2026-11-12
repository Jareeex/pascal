@extends('layouts.app')
@section('title', 'PASCAL — Pecinta Alam Sebelas Caldera')
@section('content')
<section class="hero">
    <div>
        <p class="badge">SMAN 11 BEKASI</p>
        <h1>Pecinta Alam Sebelas Caldera</h1>
        <p>Ruang untuk berkegiatan, belajar, dan menjaga alam bersama keluarga besar PASCAL.</p>
        <a class="button" href="{{ route('applications.create') }}">Gabung Bersama Kami</a>
        <a class="button secondary" href="{{ route('gallery') }}">Jelajahi Galeri</a>
    </div>
</section>
<section class="grid" style="margin-top:18px">
    @foreach([['Anggota', $stats['members']], ['Agenda', $stats['events']], ['Jurnal', $stats['stories']]] as [$label, $count])
        <div class="card" style="text-align:center"><strong style="font-size:30px;color:var(--green)">{{ $count }}</strong><div>{{ $label }}</div></div>
    @endforeach
</section>
<h2 class="section-title" id="berita">Berita Terbaru</h2>
<div class="grid grid-3">
    @forelse($news as $item)
        <article class="card">
            @if($item->gambar)
                @php($image = file_exists(public_path('assets/uploads/'.$item->gambar)) ? asset('assets/uploads/'.$item->gambar) : \Illuminate\Support\Facades\Storage::url('uploads/'.$item->gambar))
                <img class="photo small" src="{{ $image }}" alt="{{ $item->judul }}">
            @endif
            <h3>{{ $item->judul }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 180) }}</p>
            <span class="meta">{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d M Y') }}</span>
        </article>
    @empty
        <p>Belum ada berita terbaru.</p>
    @endforelse
</div>
<h2 class="section-title" id="event">Agenda Kegiatan</h2>
<div class="grid grid-3">
    @forelse($events as $event)
        <article class="card">
            <h3>{{ $event->nama_event }}</h3>
            <p class="meta">{{ $event->tanggal_mulai ? \Illuminate\Support\Carbon::parse($event->tanggal_mulai)->format('d M Y') : 'Tanggal menyusul' }} · {{ $event->lokasi ?: 'Lokasi menyusul' }}</p>
            <p>{{ \Illuminate\Support\Str::limit($event->deskripsi, 160) }}</p>
            <span class="badge">{{ $event->nama_divisi ?: 'Umum' }}</span>
        </article>
    @empty
        <p>Belum ada agenda.</p>
    @endforelse
</div>
<h2 class="section-title">Catatan Lapangan</h2>
<div class="grid grid-3">
    @forelse($stories as $story)
        <article class="card">
            @if($story->gambar)
                @php($image = file_exists(public_path('assets/uploads/'.$story->gambar)) ? asset('assets/uploads/'.$story->gambar) : \Illuminate\Support\Facades\Storage::url('uploads/'.$story->gambar))
                <img class="photo small" src="{{ $image }}" alt="{{ $story->judul }}">
            @endif
            <h3>{{ $story->judul }}</h3><p>{{ \Illuminate\Support\Str::limit($story->isi, 150) }}</p>
            <span class="meta">{{ $story->penulis ?: 'Anggota PASCAL' }} · {{ \Illuminate\Support\Carbon::parse($story->created_at)->format('d M Y') }}</span>
        </article>
    @empty
        <p>Belum ada catatan lapangan.</p>
    @endforelse
</div>
<div class="actions" style="justify-content:space-between"><h2 class="section-title">Galeri Terbaru</h2><a href="{{ route('gallery') }}">Lihat semua →</a></div>
<div class="grid">
    @forelse($gallery as $photo)
        @php($image = file_exists(public_path('assets/uploads/'.$photo->file_foto)) ? asset('assets/uploads/'.$photo->file_foto) : \Illuminate\Support\Facades\Storage::url('uploads/'.$photo->file_foto))
        <a href="{{ route('gallery') }}" aria-label="{{ $photo->judul_foto }}"><img class="photo" src="{{ $image }}" alt="{{ $photo->judul_foto }}"></a>
    @empty
        <p>Belum ada foto galeri.</p>
    @endforelse
</div>
@endsection
