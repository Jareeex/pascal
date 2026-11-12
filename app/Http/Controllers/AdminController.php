<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    use HandlesUploads;

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'members' => DB::table('users')->count(),
                'pending' => DB::table('pendaftaran')->where('status', 'pending')->count(),
                'news' => DB::table('berita')->count(),
                'events' => DB::table('events')->count(),
                'stories' => DB::table('stories')->count(),
            ],
        ]);
    }

    public function members(Request $request): View
    {
        $edit = $request->filled('edit') ? User::find($request->integer('edit')) : null;

        return view('admin.members', [
            'members' => DB::table('users')->leftJoin('divisi', 'users.divisi_id', '=', 'divisi.id')
                ->select('users.*', 'divisi.nama_divisi')->orderByDesc('users.created_at')->paginate(25),
            'divisions' => DB::table('divisi')->orderBy('nama_divisi')->get(),
            'edit' => $edit,
        ]);
    }

    public function storeMember(Request $request): RedirectResponse
    {
        $data = $this->validateMember($request, true);
        User::create($data);

        return redirect()->route('admin.members')->with('success', 'Akun anggota berhasil dibuat.');
    }

    public function updateMember(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $data = $this->validateMember($request, false, $id);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('admin.members')->with('success', 'Data anggota berhasil diperbarui.');
    }

    private function validateMember(Request $request, bool $passwordRequired, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($ignoreId)],
            'nia' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nia')->ignore($ignoreId)],
            'angkatan' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['Siswa Aktif', 'Alumni'])],
            'divisi_id' => ['nullable', 'integer', 'exists:divisi,id'],
            'role' => ['required', Rule::in(['admin', 'pengurus', 'anggota'])],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'string', 'min:8'],
        ]);
    }

    public function deleteMember(int $id): RedirectResponse
    {
        abort_if($id === Auth::id(), 403, 'Anda tidak dapat menghapus akun sendiri.');
        DB::transaction(function () use ($id): void {
            $statusIds = DB::table('status_user')->where('user_id', $id)->pluck('id');
            DB::table('komentar_status')->whereIn('status_id', $statusIds)->delete();
            DB::table('komentar_status')->where('user_id', $id)->delete();
            DB::table('status_user')->where('user_id', $id)->delete();
            DB::table('stories')->where('user_id', $id)->delete();
            DB::table('pesan')->where('pengirim_id', $id)->orWhere('penerima_id', $id)->delete();
            User::whereKey($id)->delete();
        });

        return redirect()->route('admin.members')->with('success', 'Akun anggota berhasil dihapus.');
    }

    public function news(): View
    {
        return view('admin.news', ['news' => DB::table('berita')->leftJoin('users', 'berita.author_id', '=', 'users.id')
            ->select('berita.*', 'users.nama as penulis')->orderByDesc('berita.created_at')->paginate(20)]);
    }

    public function storeNews(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:50000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);
        DB::table('berita')->insert([
            'judul' => $data['judul'],
            'slug' => Str::slug($data['judul']).'-'.Str::lower(Str::random(6)),
            'isi' => $data['isi'],
            'gambar' => $request->hasFile('gambar') ? $this->saveImage($request->file('gambar')) : null,
            'author_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return redirect()->route('admin.news')->with('success', 'Berita berhasil diterbitkan.');
    }

    public function deleteNews(int $id): RedirectResponse
    {
        $news = DB::table('berita')->where('id', $id)->first();
        abort_unless($news, 404);
        $this->deleteImage($news->gambar);
        DB::table('berita')->where('id', $id)->delete();

        return back()->with('success', 'Berita berhasil dihapus.');
    }

    public function events(): View
    {
        return view('admin.events', [
            'events' => DB::table('events')->leftJoin('divisi', 'events.divisi_id', '=', 'divisi.id')
                ->select('events.*', 'divisi.nama_divisi')->orderByDesc('tanggal_mulai')->paginate(20),
            'divisions' => DB::table('divisi')->orderBy('nama_divisi')->get(),
        ]);
    }

    public function storeEvent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_event' => ['required', 'string', 'max:200'],
            'divisi_id' => ['nullable', 'integer', 'exists:divisi,id'],
            'lokasi' => ['nullable', 'string', 'max:250'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'deskripsi' => ['nullable', 'string', 'max:10000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);
        $image = $request->hasFile('gambar') ? $this->saveImage($request->file('gambar'), 'event') : null;
        unset($data['gambar']);
        DB::table('events')->insert([
            ...$data,
            'gambar' => $image,
        ]);

        return redirect()->route('admin.events')->with('success', 'Agenda event berhasil ditambahkan.');
    }

    public function deleteEvent(int $id): RedirectResponse
    {
        $event = DB::table('events')->where('id', $id)->first();
        abort_unless($event, 404);
        $this->deleteImage($event->gambar);
        DB::table('events')->where('id', $id)->delete();

        return back()->with('success', 'Event berhasil dihapus.');
    }

    public function applications(): View
    {
        return view('admin.applications', [
            'applications' => DB::table('pendaftaran')->orderByDesc('created_at')->paginate(25),
            'open' => DB::table('settings')->where('setting_key', 'pendaftaran_buka')->value('setting_value') !== '0',
        ]);
    }

    public function toggleApplications(Request $request): RedirectResponse
    {
        $data = $request->validate(['open' => ['required', 'boolean']]);
        DB::table('settings')->updateOrInsert(
            ['setting_key' => 'pendaftaran_buka'],
            ['setting_value' => $data['open'] ? '1' : '0'],
        );

        return back()->with('success', $data['open'] ? 'Pendaftaran dibuka.' : 'Pendaftaran ditutup.');
    }

    public function updateApplication(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'diterima', 'ditolak'])]]);
        $temporaryPassword = null;

        DB::transaction(function () use ($id, $data, &$temporaryPassword): void {
            $application = DB::table('pendaftaran')->where('id', $id)->lockForUpdate()->first();
            abort_unless($application, 404);
            DB::table('pendaftaran')->where('id', $id)->update(['status' => $data['status']]);

            if ($data['status'] === 'diterima' && ! User::where('email', $application->email)->exists()) {
                $temporaryPassword = Str::random(14);
                User::create([
                    'nama' => $application->nama_lengkap,
                    'email' => $application->email,
                    'password' => Hash::make($temporaryPassword),
                    'role' => 'anggota',
                    'divisi_id' => DB::table('divisi')->where('nama_divisi', $application->divisi_minat)->value('id'),
                ]);
            }
        });

        $response = back()->with('success', 'Status pendaftar berhasil diperbarui.');
        if ($temporaryPassword) {
            $response->with('temporary_password', $temporaryPassword);
        }

        return $response;
    }

    public function deleteApplication(int $id): RedirectResponse
    {
        DB::table('pendaftaran')->where('id', $id)->delete();

        return back()->with('success', 'Data pendaftar berhasil dihapus.');
    }
}
