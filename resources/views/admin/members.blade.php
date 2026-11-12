@extends('layouts.app')
@section('title', 'Kelola Anggota')
@section('content')
<h1>Kelola Anggota</h1>
<section class="panel">
    <h2>{{ $edit ? 'Edit Anggota' : 'Tambah Anggota' }}</h2>
    <form class="stack" method="post" action="{{ $edit ? route('admin.members.update', $edit->id) : route('admin.members.store') }}">
        @csrf @if($edit)@method('put')@endif
        <div class="grid">
            <div><label>Nama</label><input name="nama" maxlength="100" value="{{ old('nama', $edit->nama ?? '') }}" required></div>
            <div><label>Email</label><input type="email" name="email" maxlength="100" value="{{ old('email', $edit->email ?? '') }}" required></div>
            <div><label>NIA</label><input name="nia" maxlength="50" value="{{ old('nia', $edit->nia ?? '') }}"></div>
            <div><label>Angkatan</label><input name="angkatan" maxlength="50" value="{{ old('angkatan', $edit->angkatan ?? '') }}"></div>
            <div><label>Status</label><select name="status"><option @selected(($edit->status ?? '') === 'Siswa Aktif')>Siswa Aktif</option><option @selected(($edit->status ?? '') === 'Alumni')>Alumni</option></select></div>
            <div><label>Divisi</label><select name="divisi_id"><option value="">Umum</option>@foreach($divisions as $division)<option value="{{ $division->id }}" @selected(($edit->divisi_id ?? null) == $division->id)>{{ $division->nama_divisi }}</option>@endforeach</select></div>
            <div><label>Peran</label><select name="role">@foreach(['anggota','pengurus','admin'] as $role)<option value="{{ $role }}" @selected(($edit->role ?? 'anggota') === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
            <div><label>Password {{ $edit ? '(kosongkan jika tidak diubah)' : '(minimal 8 karakter)' }}</label><input type="password" name="password" minlength="8" {{ $edit ? '' : 'required' }}></div>
        </div>
        <div class="actions"><button type="submit">{{ $edit ? 'Simpan perubahan' : 'Tambah anggota' }}</button>@if($edit)<a class="button secondary" href="{{ route('admin.members') }}">Batal</a>@endif</div>
    </form>
</section>
<section class="panel">
    <h2>Daftar Anggota</h2>
    <div class="table-wrap"><table><thead><tr><th>Nama</th><th>Email</th><th>NIA</th><th>Angkatan</th><th>Divisi</th><th>Peran</th><th>Aksi</th></tr></thead><tbody>
    @foreach($members as $member)
        <tr><td>{{ $member->nama }}</td><td>{{ $member->email }}</td><td>{{ $member->nia ?: '—' }}</td><td>{{ $member->angkatan ?: '—' }}</td><td>{{ $member->nama_divisi ?: 'Umum' }}</td><td>{{ ucfirst($member->role) }}</td>
        <td><div class="actions"><a class="button secondary" href="{{ route('admin.members', ['edit' => $member->id]) }}">Edit</a>
            @if($member->id !== auth()->id())<form method="post" action="{{ route('admin.members.delete', $member->id) }}">@csrf @method('delete')<button class="danger" type="submit">Hapus</button></form>@endif
        </div></td></tr>
    @endforeach
    </tbody></table></div>
    {{ $members->links() }}
</section>
@endsection
