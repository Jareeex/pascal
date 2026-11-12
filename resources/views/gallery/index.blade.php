@extends('layouts.app')
@section('title', 'Galeri PASCAL')
@section('content')
<h1>Galeri Kegiatan</h1>
<p class="meta">Dokumentasi kegiatan keluarga besar PASCAL.</p>
@auth
    <p><a class="button" href="{{ route('members.gallery.create') }}">Unggah foto</a></p>
@endauth
<div class="grid grid-3">
    @forelse($photos as $photo)
        @php($image = file_exists(public_path('assets/uploads/'.$photo->file_foto)) ? asset('assets/uploads/'.$photo->file_foto) : \Illuminate\Support\Facades\Storage::url('uploads/'.$photo->file_foto))
        <article class="card">
            <img class="photo" src="{{ $image }}" alt="{{ $photo->judul_foto }}">
            <h3>{{ $photo->judul_foto ?: 'Dokumentasi PASCAL' }}</h3>
            <p class="meta">{{ $photo->nama_event ?: 'Kegiatan umum' }} · {{ \Illuminate\Support\Carbon::parse($photo->uploaded_at)->format('d M Y') }}</p>
            @foreach($comments->get($photo->id, collect()) as $comment)
                <div class="comment"><strong>{{ $comment->nama_pengirim }}</strong><br>{{ $comment->isi_komentar }}</div>
            @endforeach
            <form class="stack" method="post" action="{{ route('gallery.comments.store', $photo->id) }}">
                @csrf
                @guest<label>Nama</label><input name="nama_pengirim" maxlength="100" required>@endguest
                <label>Komentar</label><input name="isi_komentar" maxlength="2000" required>
                <button type="submit">Kirim komentar</button>
            </form>
        </article>
    @empty
        <p>Belum ada foto.</p>
    @endforelse
</div>
@endsection
