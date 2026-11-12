<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'members.profile');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_input' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ]);
        $field = filter_var($validated['login_input'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nia';

        if (! Auth::attempt([$field => $validated['login_input'], 'password' => $validated['password']], $request->boolean('remember'))) {
            return back()->withErrors(['login_input' => 'Email/NIA atau password salah.'])->onlyInput('login_input');
        }

        $request->session()->regenerate();

        return redirect()->intended(route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'members.profile'));
    }

    public function registerForm(): View
    {
        return view('auth.register', ['divisions' => DB::table('divisi')->orderBy('nama_divisi')->get()]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'angkatan' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['Siswa Aktif', 'Alumni'])],
            'divisi_id' => ['required', 'integer', 'exists:divisi,id'],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'angkatan' => $validated['angkatan'],
            'status' => $validated['status'],
            'divisi_id' => $validated['divisi_id'],
            'role' => 'anggota',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('members.profile')->with('success', 'Pendaftaran berhasil. Selamat datang di Pascal!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
