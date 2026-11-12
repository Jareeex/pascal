@extends('layouts.app')
@section('title', 'Jurnal Lapangan PASCAL')
@section('content')
<div class="actions" style="justify-content:space-between"><div><h1>Jurnal Lapangan</h1><p class="meta">Cerita, pengalaman, dan catatan perjalanan anggota.</p></div>@auth<a class="button" href="{{ route('members.stories.create') }}">Tulis cerita</a>@endauth</div>
<div class="grid grid-3">
    @forelse($stories as $story)
        <article class="card">
            @if($story->gambar)
                @php($image = file_exists(public_path('assets/uploads/'.$story->gambar)) ? asset('assets/uploads/'.$story->gambar) : \Illuminate\Support\Facades\Storage::url('uploads/'.$story->gambar))
                <img class="photo" src="{{ $image }}" alt="{{ $story->judul }}">
            @endif
            <h2>{{ $story->judul }}</h2><p class="meta">{{ $story->lokasi ?: 'Lokasi tidak dicantumkan' }} · {{ $story->penulis ?: 'Anggota PASCAL' }}</p>
            <p style="white-space:pre-line">{{ \Illuminate\Support\Str::limit($story->isi, 420) }}</p>
            <span class="meta">{{ \Illuminate\Support\Carbon::parse($story->created_at)->format('d M Y') }}</span>
        </article>
    @empty
        <p>Belum ada catatan yang diterbitkan.</p>
    @endforelse
</div>
<div class="pagination">{{ $stories->links() }}</div>
@endsection
