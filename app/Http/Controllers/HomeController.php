<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'slides' => DB::table('galeri')->orderByDesc('uploaded_at')->limit(5)->get(),
            'news' => DB::table('berita')->orderByDesc('created_at')->limit(3)->get(),
            'events' => DB::table('events')->leftJoin('divisi', 'events.divisi_id', '=', 'divisi.id')
                ->select('events.*', 'divisi.nama_divisi')->orderBy('tanggal_mulai')->limit(3)->get(),
            'stories' => DB::table('stories')->leftJoin('users', 'stories.user_id', '=', 'users.id')
                ->select('stories.*', 'users.nama as penulis')->orderByDesc('stories.created_at')->limit(3)->get(),
            'gallery' => DB::table('galeri')->leftJoin('events', 'galeri.event_id', '=', 'events.id')
                ->select('galeri.*', 'events.nama_event')->orderByDesc('uploaded_at')->limit(6)->get(),
            'stats' => [
                'members' => DB::table('users')->count(),
                'events' => DB::table('events')->count(),
                'stories' => DB::table('stories')->count(),
            ],
        ]);
    }

    public function gallery(): View
    {
        $photos = DB::table('galeri')
            ->leftJoin('events', 'galeri.event_id', '=', 'events.id')
            ->select('galeri.*', 'events.nama_event')
            ->orderByDesc('uploaded_at')
            ->get();

        $comments = DB::table('komentar_galeri')
            ->whereIn('galeri_id', $photos->pluck('id'))
            ->orderBy('created_at')
            ->get()
            ->groupBy('galeri_id');

        return view('gallery.index', compact('photos', 'comments'));
    }

    public function journal(): View
    {
        $stories = DB::table('stories')
            ->leftJoin('users', 'stories.user_id', '=', 'users.id')
            ->select('stories.*', 'users.nama as penulis')
            ->orderByDesc('stories.created_at')
            ->paginate(12);

        return view('journal.index', compact('stories'));
    }
}
