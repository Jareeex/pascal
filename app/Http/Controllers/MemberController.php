<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    use HandlesUploads;

    public function directory(Request $request): View
    {
        $query = DB::table('users')->leftJoin('divisi', 'users.divisi_id', '=', 'divisi.id')
            ->select('users.id', 'users.nama', 'users.nia', 'users.foto_profil', 'users.angkatan', 'users.status', 'divisi.nama_divisi');

        if ($search = trim((string) $request->query('search'))) {
            $query->where('users.nama', 'like', '%'.$search.'%');
        }
        if ($angkatan = $request->query('angkatan')) {
            $query->where('users.angkatan', $angkatan);
        }

        return view('members.directory', [
            'members' => $query->orderBy('users.nama')->paginate(18)->withQueryString(),
            'angkatan' => DB::table('users')->whereNotNull('angkatan')->where('angkatan', '!=', '')
                ->distinct()->orderBy('angkatan')->pluck('angkatan'),
        ]);
    }

    public function profile(int $id = 0): View
    {
        $userId = $id ?: Auth::id();
        $profile = DB::table('users')->leftJoin('divisi', 'users.divisi_id', '=', 'divisi.id')
            ->select('users.*', 'divisi.nama_divisi')->where('users.id', $userId)->first();
        abort_unless($profile, 404);

        $statuses = DB::table('status_user')->where('user_id', $userId)->orderByDesc('created_at')->get();
        $comments = DB::table('komentar_status')->join('users', 'komentar_status.user_id', '=', 'users.id')
            ->whereIn('status_id', $statuses->pluck('id'))
            ->select('komentar_status.*', 'users.nama', 'users.foto_profil')
            ->orderBy('komentar_status.created_at')->get()->groupBy('status_id');

        $stories = DB::table('stories')->where('user_id', $userId)->orderByDesc('created_at')->get();
        $inbox = $userId === Auth::id()
            ? DB::table('pesan')->join('users', 'pesan.pengirim_id', '=', 'users.id')
                ->where('pesan.penerima_id', $userId)
                ->select('pesan.*', 'users.nama as nama_pengirim')->orderByDesc('pesan.created_at')->get()
            : collect();

        return view('members.profile', [
            'profile' => $profile,
            'isOwnProfile' => $userId === Auth::id(),
            'divisions' => DB::table('divisi')->orderBy('nama_divisi')->get(),
            'statuses' => $statuses,
            'comments' => $comments,
            'stories' => $stories,
            'inbox' => $inbox,
            'members' => DB::table('users')->where('id', '!=', Auth::id())->orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nia' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nia')->ignore(Auth::id())],
            'status' => ['required', Rule::in(['Siswa Aktif', 'Alumni'])],
            'angkatan' => ['nullable', 'string', 'max:50'],
            'divisi_id' => ['nullable', 'integer', 'exists:divisi,id'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $user = Auth::user();

        if ($request->hasFile('foto_profil')) {
            $this->deleteImage($user->foto_profil);
            $validated['foto_profil'] = $this->saveImage($request->file('foto_profil'), 'profile');
        }
        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function postStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate(['isi_status' => ['required', 'string', 'max:5000']]);
        DB::table('status_user')->insert(['user_id' => Auth::id(), 'isi_status' => $validated['isi_status'], 'created_at' => now()]);

        return back()->with('success', 'Status berhasil dibagikan.');
    }

    public function deleteStatus(int $id): RedirectResponse
    {
        abort_unless(DB::table('status_user')->where('id', $id)->where('user_id', Auth::id())->exists(), 404);
        DB::transaction(function () use ($id): void {
            DB::table('komentar_status')->where('status_id', $id)->delete();
            DB::table('status_user')->where('id', $id)->delete();
        });

        return back()->with('success', 'Status berhasil dihapus.');
    }

    public function postComment(Request $request, int $status): RedirectResponse
    {
        abort_unless(DB::table('status_user')->where('id', $status)->exists(), 404);
        $validated = $request->validate(['isi_komentar' => ['required', 'string', 'max:2000']]);
        DB::table('komentar_status')->insert([
            'status_id' => $status, 'user_id' => Auth::id(),
            'isi_komentar' => $validated['isi_komentar'], 'created_at' => now(),
        ]);

        return back()->with('success', 'Komentar berhasil dikirim.');
    }

    public function deleteComment(int $id): RedirectResponse
    {
        $comment = DB::table('komentar_status')->join('status_user', 'komentar_status.status_id', '=', 'status_user.id')
            ->where('komentar_status.id', $id)
            ->where(function ($query): void {
                $query->where('komentar_status.user_id', Auth::id())->orWhere('status_user.user_id', Auth::id());
            })
            ->select('komentar_status.id')->first();
        abort_unless($comment, 404);
        DB::table('komentar_status')->where('id', $id)->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'penerima_id' => ['required', 'integer', 'exists:users,id', Rule::notIn([Auth::id()])],
            'subjek' => ['required', 'string', 'max:255'],
            'isi_pesan' => ['required', 'string', 'max:10000'],
        ]);
        DB::table('pesan')->insert([
            'pengirim_id' => Auth::id(), 'penerima_id' => $validated['penerima_id'],
            'subjek' => $validated['subjek'], 'isi_pesan' => $validated['isi_pesan'],
            'is_read' => false, 'created_at' => now(),
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function readMessage(int $id): RedirectResponse
    {
        DB::table('pesan')->where('id', $id)->where('penerima_id', Auth::id())->update(['is_read' => true]);

        return back();
    }

    public function deleteMessage(int $id): RedirectResponse
    {
        DB::table('pesan')->where('id', $id)->where('penerima_id', Auth::id())->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    public function createStory(): View
    {
        return view('members.story-form');
    }

    public function storeStory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'isi' => ['required', 'string', 'max:20000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $image = $request->hasFile('foto') ? $this->saveImage($request->file('foto')) : null;
        DB::table('stories')->insert([
            'judul' => $validated['judul'], 'lokasi' => $validated['lokasi'] ?? null,
            'isi' => $validated['isi'], 'gambar' => $image, 'user_id' => Auth::id(), 'created_at' => now(),
        ]);

        return redirect()->route('members.profile')->with('success', 'Catatan lapangan berhasil diterbitkan.');
    }

    public function editStory(int $id): View
    {
        $story = DB::table('stories')->where('id', $id)->where('user_id', Auth::id())->first();
        abort_unless($story, 404);

        return view('members.story-form', compact('story'));
    }

    public function updateStory(Request $request, int $id): RedirectResponse
    {
        $story = DB::table('stories')->where('id', $id)->where('user_id', Auth::id())->first();
        abort_unless($story, 404);
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'isi' => ['required', 'string', 'max:20000'],
        ]);
        DB::table('stories')->where('id', $id)->update($validated);

        return redirect()->route('members.profile')->with('success', 'Catatan lapangan berhasil diperbarui.');
    }

    public function deleteStory(int $id): RedirectResponse
    {
        $story = DB::table('stories')->where('id', $id)->where('user_id', Auth::id())->first();
        abort_unless($story, 404);
        $this->deleteImage($story->gambar);
        DB::table('stories')->where('id', $id)->delete();

        return back()->with('success', 'Catatan lapangan berhasil dihapus.');
    }

    public function createGalleryPhoto(): View
    {
        return view('members.gallery-form', ['events' => DB::table('events')->orderByDesc('tanggal_mulai')->get()]);
    }

    public function storeGalleryPhoto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul_foto' => ['required', 'string', 'max:150'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'file_foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        DB::table('galeri')->insert([
            'judul_foto' => $validated['judul_foto'],
            'event_id' => $validated['event_id'] ?? null,
            'file_foto' => $this->saveImage($request->file('file_foto'), 'galeri'),
            'uploaded_at' => now(),
        ]);

        return redirect()->route('gallery')->with('success', 'Foto berhasil diunggah ke galeri.');
    }
}
