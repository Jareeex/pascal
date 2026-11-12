@extends('layouts.app')
@section('title', isset($story) ? 'Edit Jurnal' : 'Tambah Jurnal')
@section('content')
<div class="card" style="max-width:760px;margin:auto">
    <h1>{{ isset($story) ? 'Edit Catatan Lapangan' : 'Tulis Catatan Lapangan' }}</h1>
    <form class="stack" method="post" enctype="multipart/form-data" action="{{ isset($story) ? route('members.stories.update', $story->id) : route('members.stories.store') }}">
        @csrf @if(isset($story))@method('put')@endif
        <label>Judul</label><input name="judul" maxlength="255" value="{{ old('judul', $story->judul ?? '') }}" required>
        <label>Lokasi</label><input name="lokasi" maxlength="150" value="{{ old('lokasi', $story->lokasi ?? '') }}">
        @unless(isset($story))<label>Foto (opsional, maks. 5 MB)</label><input type="file" name="foto" accept="image/jpeg,image/png,image/webp">@endunless
        <label>Cerita</label><textarea name="isi" maxlength="20000" required>{{ old('isi', $story->isi ?? '') }}</textarea>
        <button type="submit">{{ isset($story) ? 'Simpan perubahan' : 'Terbitkan jurnal' }}</button>
    </form>
</div>
@endsection
