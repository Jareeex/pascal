@extends('layouts.app')
@section('title', 'Kelola Agenda')
@section('content')
<h1>Kelola Agenda Kegiatan</h1>
<section class="panel">
    <h2>Tambah event</h2>
    <form class="stack" method="post" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Nama event</label><input name="nama_event" maxlength="200" required>
        <label>Divisi penyelenggara</label><select name="divisi_id"><option value="">Umum / semua divisi</option>@foreach($divisions as $division)<option value="{{ $division->id }}">{{ $division->nama_divisi }}</option>@endforeach</select>
        <div class="grid"><div><label>Tanggal mulai</label><input type="date" name="tanggal_mulai" required></div><div><label>Tanggal selesai (opsional)</label><input type="date" name="tanggal_selesai"></div></div>
        <label>Lokasi</label><input name="lokasi" maxlength="250">
        <label>Gambar (opsional)</label><input type="file" name="gambar" accept="image/*">
        <label>Deskripsi</label><textarea name="deskripsi" maxlength="10000"></textarea>
        <button type="submit">Simpan event</button>
    </form>
</section>
<section class="panel">
    <div class="table-wrap"><table><thead><tr><th>Event</th><th>Divisi</th><th>Lokasi</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>
    @forelse($events as $event)
        <tr><td>{{ $event->nama_event }}</td><td>{{ $event->nama_divisi ?: 'Umum' }}</td><td>{{ $event->lokasi ?: '—' }}</td><td>{{ $event->tanggal_mulai ? \Illuminate\Support\Carbon::parse($event->tanggal_mulai)->format('d M Y') : '—' }}</td>
        <td><form method="post" action="{{ route('admin.events.delete', $event->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus</button></form></td></tr>
    @empty<tr><td colspan="5">Belum ada event.</td></tr>@endforelse
    </tbody></table></div>{{ $events->links() }}
</section>
@endsection
