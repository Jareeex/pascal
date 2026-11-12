<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function form(): View
    {
        $open = DB::table('settings')->where('setting_key', 'pendaftaran_buka')->value('setting_value') !== '0';

        return view('applications.create', [
            'open' => $open,
            'divisions' => DB::table('divisi')->orderBy('nama_divisi')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(DB::table('settings')->where('setting_key', 'pendaftaran_buka')->value('setting_value') === '0', 403, 'Pendaftaran sedang ditutup.');
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nama_panggilan' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'tempat_lahir' => ['nullable', 'string', 'max:50'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'alamat' => ['nullable', 'string', 'max:5000'],
            'divisi_minat' => ['nullable', 'string', 'max:50'],
            'alasan_bergabung' => ['nullable', 'string', 'max:5000'],
        ]);
        $validated['status'] = 'pending';
        $validated['created_at'] = now();
        DB::table('pendaftaran')->insert($validated);

        return redirect()->route('applications.create')->with('success', 'Pendaftaran terkirim. Admin akan meninjau data Anda.');
    }
}
