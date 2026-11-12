@extends('layouts.app')
@section('title', 'Profil '.$profile->nama)
@section('content')
<div class="card">
    <div class="actions">
        @if($profile->foto_profil)
            @php($image = file_exists(public_path('assets/uploads/'.$profile->foto_profil)) ? asset('assets/uploads/'.$profile->foto_profil) : \Illuminate\Support\Facades\Storage::url('uploads/'.$profile->foto_profil))
            <img class="profile-photo" src="{{ $image }}" alt="{{ $profile->nama }}">
        @else
            <div class="profile-photo" style="display:grid;place-items:center;font-size:42px;color:var(--green)">{{ mb_substr($profile->nama, 0, 1) }}</div>
        @endif
        <div><h1>{{ $profile->nama }}</h1><p class="meta">NIA {{ $profile->nia ?: '—' }} · {{ $profile->status ?: 'Siswa Aktif' }}</p>
            <p class="meta">Angkatan {{ $profile->angkatan ?: '—' }} · Divisi {{ $profile->nama_divisi ?: 'Umum' }}</p></div>
        <div class="actions">
            <a class="button secondary" href="{{ route('members.directory') }}">Direktori anggota</a>
            @if($isOwnProfile)
                <a class="button secondary" href="{{ route('members.stories.create') }}">Tulis jurnal</a>
                <a class="button secondary" href="{{ route('members.gallery.create') }}">Unggah foto</a>
            @endif
        </div>
    </div>
</div>

@if($isOwnProfile)
<details class="panel">
    <summary><strong>Edit profil</strong></summary>
    <form class="stack" method="post" action="{{ route('members.profile.update') }}" enctype="multipart/form-data">
        @csrf @method('put')
        <label>NIA</label><input name="nia" maxlength="50" value="{{ old('nia', $profile->nia) }}">
        <label>Status keanggotaan</label><select name="status"><option @selected($profile->status === 'Siswa Aktif')>Siswa Aktif</option><option @selected($profile->status === 'Alumni')>Alumni</option></select>
        <label>Angkatan</label><input name="angkatan" maxlength="50" value="{{ old('angkatan', $profile->angkatan) }}">
        <label>Divisi</label><select name="divisi_id"><option value="">Umum</option>@foreach($divisions as $division)<option value="{{ $division->id }}" @selected($profile->divisi_id == $division->id)>{{ $division->nama_divisi }}</option>@endforeach</select>
        <label>Foto profil (JPG, PNG, WEBP, maks. 2 MB)</label><input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp">
        <button type="submit">Simpan profil</button>
    </form>
</details>
@else
<details class="panel">
    <summary><strong>Kirim pesan kepada {{ $profile->nama }}</strong></summary>
    <form class="stack" method="post" action="{{ route('members.messages.store') }}">
        @csrf<input type="hidden" name="penerima_id" value="{{ $profile->id }}">
        <label>Subjek</label><input name="subjek" maxlength="255" value="{{ old('subjek', 'Pesan Anggota') }}" required>
        <label>Pesan</label><textarea name="isi_pesan" maxlength="10000" required></textarea>
        <button type="submit">Kirim pesan</button>
    </form>
</details>
@endif

@if($isOwnProfile)
<section class="panel">
    <h2>Status saya</h2>
    <form class="stack" method="post" action="{{ route('members.status.store') }}">@csrf
        <label>Tulis status</label><textarea name="isi_status" maxlength="5000" required></textarea><button type="submit">Bagikan status</button>
    </form>
</section>
@else
<h2 class="section-title">Status {{ $profile->nama }}</h2>
@endif

@forelse($statuses as $status)
    <article class="status-card">
        <div class="actions" style="justify-content:space-between"><span class="meta">{{ \Illuminate\Support\Carbon::parse($status->created_at)->format('d M Y H:i') }}</span>
            @if($isOwnProfile)<form method="post" action="{{ route('members.status.delete', $status->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus status</button></form>@endif
        </div>
        <p style="white-space:pre-line">{{ $status->isi_status }}</p>
        @foreach($comments->get($status->id, collect()) as $comment)
            <div class="comment"><strong>{{ $comment->nama }}</strong> · <span class="meta">{{ \Illuminate\Support\Carbon::parse($comment->created_at)->format('d M H:i') }}</span>
                <p style="white-space:pre-line">{{ $comment->isi_komentar }}</p>
                @if($comment->user_id === auth()->id() || $isOwnProfile)
                    <form method="post" action="{{ route('members.comments.delete', $comment->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus komentar</button></form>
                @endif
            </div>
        @endforeach
        <form class="inline-form" method="post" action="{{ route('members.comments.store', $status->id) }}">@csrf
            <input name="isi_komentar" maxlength="2000" placeholder="Tulis komentar..." required><button type="submit">Kirim</button>
        </form>
    </article>
@empty
    @if(!$isOwnProfile)<p class="meta">Belum ada status.</p>@endif
@endforelse

<div class="actions" style="justify-content:space-between"><h2 class="section-title">Jurnal {{ $isOwnProfile ? 'saya' : $profile->nama }}</h2>@if($isOwnProfile)<a class="button" href="{{ route('members.stories.create') }}">Tambah jurnal</a>@endif</div>
@forelse($stories as $story)
    <article class="card">
        <h3>{{ $story->judul }}</h3><span class="meta">{{ $story->lokasi ?: 'Lokasi tidak dicantumkan' }} · {{ \Illuminate\Support\Carbon::parse($story->created_at)->format('d M Y') }}</span>
        <p style="white-space:pre-line">{{ \Illuminate\Support\Str::limit($story->isi, 400) }}</p>
        @if($isOwnProfile)<div class="actions"><a class="button secondary" href="{{ route('members.stories.edit', $story->id) }}">Edit</a><form method="post" action="{{ route('members.stories.delete', $story->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus</button></form></div>@endif
    </article>
@empty
    <p class="meta">Belum ada jurnal.</p>
@endforelse

@if($isOwnProfile)
<h2 class="section-title">Kotak masuk</h2>
@forelse($inbox as $message)
    <article class="card">
        <div class="actions" style="justify-content:space-between"><strong>{{ $message->subjek }}</strong><span class="badge">{{ $message->is_read ? 'Dibaca' : 'Belum dibaca' }}</span></div>
        <p class="meta">Dari {{ $message->nama_pengirim }} · {{ \Illuminate\Support\Carbon::parse($message->created_at)->format('d M Y H:i') }}</p>
        <p style="white-space:pre-line">{{ $message->isi_pesan }}</p>
        <div class="actions">
            @unless($message->is_read)<form method="post" action="{{ route('members.messages.read', $message->id) }}">@csrf @method('patch')<button type="submit">Tandai dibaca</button></form>@endunless
            <form method="post" action="{{ route('members.messages.delete', $message->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus</button></form>
        </div>
    </article>
@empty
    <p class="meta">Kotak masuk masih kosong.</p>
@endforelse
@endif
@endsection
