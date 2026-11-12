<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryCommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cek-versi', fn () => 'Versi PHP hosting: '.PHP_VERSION)->name('version');
Route::get('/galeri', [HomeController::class, 'gallery'])->name('gallery');
Route::get('/jurnal', [HomeController::class, 'journal'])->name('journal');
Route::post('/galeri/{photo}/komentar', [GalleryCommentController::class, 'store'])->name('gallery.comments.store');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/pendaftaran', [ApplicationController::class, 'form'])->name('applications.create');
Route::post('/pendaftaran', [ApplicationController::class, 'store'])->name('applications.store');

Route::get('/anggota', [MemberController::class, 'directory'])->name('members.directory');
Route::middleware('auth')->group(function (): void {
    Route::get('/profil', [MemberController::class, 'profile'])->name('members.profile');
    Route::get('/anggota/{id}', [MemberController::class, 'profile'])->whereNumber('id')->name('members.show');
    Route::put('/profil', [MemberController::class, 'updateProfile'])->name('members.profile.update');
    Route::post('/status', [MemberController::class, 'postStatus'])->name('members.status.store');
    Route::delete('/status/{id}', [MemberController::class, 'deleteStatus'])->whereNumber('id')->name('members.status.delete');
    Route::post('/status/{status}/komentar', [MemberController::class, 'postComment'])->whereNumber('status')->name('members.comments.store');
    Route::delete('/komentar/{id}', [MemberController::class, 'deleteComment'])->whereNumber('id')->name('members.comments.delete');
    Route::post('/pesan', [MemberController::class, 'sendMessage'])->name('members.messages.store');
    Route::patch('/pesan/{id}/dibaca', [MemberController::class, 'readMessage'])->whereNumber('id')->name('members.messages.read');
    Route::delete('/pesan/{id}', [MemberController::class, 'deleteMessage'])->whereNumber('id')->name('members.messages.delete');
    Route::get('/jurnal/tambah', [MemberController::class, 'createStory'])->name('members.stories.create');
    Route::post('/jurnal', [MemberController::class, 'storeStory'])->name('members.stories.store');
    Route::get('/jurnal/{id}/edit', [MemberController::class, 'editStory'])->whereNumber('id')->name('members.stories.edit');
    Route::put('/jurnal/{id}', [MemberController::class, 'updateStory'])->whereNumber('id')->name('members.stories.update');
    Route::delete('/jurnal/{id}', [MemberController::class, 'deleteStory'])->whereNumber('id')->name('members.stories.delete');
    Route::get('/galeri/tambah', [MemberController::class, 'createGalleryPhoto'])->name('members.gallery.create');
    Route::post('/galeri', [MemberController::class, 'storeGalleryPhoto'])->name('members.gallery.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/anggota', [AdminController::class, 'members'])->name('members');
    Route::post('/anggota', [AdminController::class, 'storeMember'])->name('members.store');
    Route::put('/anggota/{id}', [AdminController::class, 'updateMember'])->whereNumber('id')->name('members.update');
    Route::delete('/anggota/{id}', [AdminController::class, 'deleteMember'])->whereNumber('id')->name('members.delete');
    Route::get('/berita', [AdminController::class, 'news'])->name('news');
    Route::post('/berita', [AdminController::class, 'storeNews'])->name('news.store');
    Route::delete('/berita/{id}', [AdminController::class, 'deleteNews'])->whereNumber('id')->name('news.delete');
    Route::get('/event', [AdminController::class, 'events'])->name('events');
    Route::post('/event', [AdminController::class, 'storeEvent'])->name('events.store');
    Route::delete('/event/{id}', [AdminController::class, 'deleteEvent'])->whereNumber('id')->name('events.delete');
    Route::get('/pendaftaran', [AdminController::class, 'applications'])->name('applications');
    Route::patch('/pendaftaran/status', [AdminController::class, 'toggleApplications'])->name('applications.toggle');
    Route::patch('/pendaftaran/{id}', [AdminController::class, 'updateApplication'])->whereNumber('id')->name('applications.update');
    Route::delete('/pendaftaran/{id}', [AdminController::class, 'deleteApplication'])->whereNumber('id')->name('applications.delete');
});

Route::prefix('pengurus')->name('officers.')->middleware(['auth', 'role:admin,pengurus'])->group(function (): void {
    Route::get('/pendaftaran', [AdminController::class, 'applications'])->name('applications');
    Route::patch('/pendaftaran/status', [AdminController::class, 'toggleApplications'])->name('applications.toggle');
    Route::patch('/pendaftaran/{id}', [AdminController::class, 'updateApplication'])->whereNumber('id')->name('applications.update');
    Route::delete('/pendaftaran/{id}', [AdminController::class, 'deleteApplication'])->whereNumber('id')->name('applications.delete');
});
