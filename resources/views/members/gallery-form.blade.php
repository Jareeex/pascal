@extends('layouts.app')
@section('title', 'Unggah Foto Galeri')
@section('content')
<div class="card" style="max-width:650px;margin:auto">
    <h1>Unggah Foto Galeri</h1>
    <form class="stack" method="post" action="{{ route('members.gallery.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Judul foto</label><input name="judul_foto" maxlength="150" required>
        <label>Event terkait</label><select name="event_id"><option value="">Kegiatan umum</option>@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->nama_event }}</option>@endforeach</select>
        <label>Foto (JPG, PNG, WEBP, maks. 5 MB)</label><input type="file" name="file_foto" accept="image/jpeg,image/png,image/webp" required>
        <button type="submit">Unggah foto</button>
    </form>
</div>
@endsection
