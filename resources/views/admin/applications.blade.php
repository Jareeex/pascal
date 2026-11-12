@extends('layouts.app')
@section('title', 'Penerimaan Anggota')
@section('content')
<h1>Penerimaan Calon Anggota</h1>
<section class="panel actions" style="justify-content:space-between">
    <strong>Pendaftaran {{ $open ? 'dibuka' : 'ditutup' }}</strong>
    <form method="post" action="{{ request()->routeIs('officers.*') ? route('officers.applications.toggle') : route('admin.applications.toggle') }}">
        @csrf @method('patch')<input type="hidden" name="open" value="{{ $open ? 0 : 1 }}">
        <button type="submit">{{ $open ? 'Tutup pendaftaran' : 'Buka pendaftaran' }}</button>
    </form>
</section>
@forelse($applications as $applicant)
    <article class="card">
        <div class="actions" style="justify-content:space-between">
            <div><h2>{{ $applicant->nama_lengkap }} <span class="badge">{{ ucfirst($applicant->status) }}</span></h2>
                <p class="meta">{{ $applicant->email }} · {{ $applicant->nomor_telepon }} · {{ $applicant->divisi_minat ?: 'Divisi belum dipilih' }}</p></div>
            <span class="meta">{{ \Illuminate\Support\Carbon::parse($applicant->created_at)->format('d M Y') }}</span>
        </div>
        <p><strong>Alamat:</strong> {{ $applicant->alamat ?: '—' }}</p>
        <p><strong>Alasan bergabung:</strong><br>{{ $applicant->alasan_bergabung ?: '—' }}</p>
        <div class="actions">
            @foreach(['pending' => 'Menunggu', 'diterima' => 'Terima', 'ditolak' => 'Tolak'] as $status => $label)
                <form method="post" action="{{ request()->routeIs('officers.*') ? route('officers.applications.update', $applicant->id) : route('admin.applications.update', $applicant->id) }}">
                    @csrf @method('patch')<input type="hidden" name="status" value="{{ $status }}"><button type="submit">{{ $label }}</button>
                </form>
            @endforeach
            <form method="post" action="{{ request()->routeIs('officers.*') ? route('officers.applications.delete', $applicant->id) : route('admin.applications.delete', $applicant->id) }}">
                @csrf @method('delete')<button class="danger" type="submit">Hapus pendaftar</button>
            </form>
        </div>
    </article>
@empty
    <p>Belum ada pendaftar.</p>
@endforelse
{{ $applications->links() }}
@endsection
