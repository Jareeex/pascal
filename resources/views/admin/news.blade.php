@extends('layouts.app')
@section('title', 'Kelola Berita')
@section('content')
<h1>Kelola Berita</h1>
<section class="panel">
    <h2>Terbitkan berita</h2>
    <form class="stack" method="post" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Judul</label><input name="judul" maxlength="255" required>
        <label>Foto berita (opsional)</label><input type="file" name="gambar" accept="image/*">
        <label>Isi berita</label><textarea name="isi" maxlength="50000" required></textarea>
        <button type="submit">Terbitkan</button>
    </form>
</section>
<section class="panel">
    <div class="table-wrap"><table><thead><tr><th>Judul</th><th>Penulis</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>
    @forelse($news as $item)
        <tr><td>{{ $item->judul }}</td><td>{{ $item->penulis ?: 'Admin' }}</td><td>{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d M Y') }}</td>
        <td><form method="post" action="{{ route('admin.news.delete', $item->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus</button></form></td></tr>
    @empty<tr><td colspan="4">Belum ada berita.</td></tr>@endforelse
    </tbody></table></div>{{ $news->links() }}
</section>
@endsection
