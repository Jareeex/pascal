<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GalleryCommentController extends Controller
{
    public function store(Request $request, int $photo): RedirectResponse
    {
        abort_unless(DB::table('galeri')->where('id', $photo)->exists(), 404);
        $validated = $request->validate([
            'nama_pengirim' => [Auth::check() ? 'nullable' : 'required', 'string', 'max:100'],
            'isi_komentar' => ['required', 'string', 'max:2000'],
        ]);
        DB::table('komentar_galeri')->insert([
            'galeri_id' => $photo,
            'nama_pengirim' => Auth::user()->nama ?? $validated['nama_pengirim'],
            'isi_komentar' => $validated['isi_komentar'],
            'created_at' => now(),
        ]);

        return back()->with('success', 'Komentar berhasil dikirim.');
    }
}
