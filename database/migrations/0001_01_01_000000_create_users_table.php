<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('berita')) {
            Schema::create('berita', function (Blueprint $table) {
                $table->increments('id');
                $table->string('judul');
                $table->string('slug');
                $table->text('isi');
                $table->string('gambar')->nullable();
                $table->unsignedInteger('author_id')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('divisi')) {
            Schema::create('divisi', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_divisi', 100);
                $table->text('deskripsi')->nullable();
                $table->string('logo')->nullable();
            });
        }

        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_event', 200);
                $table->text('deskripsi')->nullable();
                $table->string('gambar')->nullable();
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->string('lokasi', 250)->nullable();
                $table->unsignedInteger('divisi_id')->nullable();
            });
        }

        if (! Schema::hasTable('galeri')) {
            Schema::create('galeri', function (Blueprint $table) {
                $table->increments('id');
                $table->string('judul_foto', 150)->nullable();
                $table->string('file_foto');
                $table->unsignedInteger('event_id')->nullable();
                $table->timestamp('uploaded_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('komentar_galeri')) {
            Schema::create('komentar_galeri', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('galeri_id');
                $table->string('nama_pengirim', 100);
                $table->text('isi_komentar');
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('komentar_status')) {
            Schema::create('komentar_status', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('status_id');
                $table->unsignedInteger('user_id');
                $table->text('isi_komentar');
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('pendaftaran')) {
            Schema::create('pendaftaran', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_lengkap', 100);
                $table->string('nama_panggilan', 50)->nullable();
                $table->string('email', 100);
                $table->string('nomor_telepon', 20);
                $table->string('tempat_lahir', 50)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
                $table->text('alamat')->nullable();
                $table->string('divisi_minat', 50)->nullable();
                $table->text('alasan_bergabung')->nullable();
                $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('pesan')) {
            Schema::create('pesan', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('pengirim_id');
                $table->unsignedInteger('penerima_id');
                $table->string('subjek')->nullable()->default('Pesan Anggota');
                $table->text('isi_pesan');
                $table->boolean('is_read')->default(false);
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->string('setting_key', 50)->primary();
                $table->text('setting_value')->nullable();
            });
        }

        if (! Schema::hasTable('status_user')) {
            Schema::create('status_user', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->text('isi_status');
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('stories')) {
            Schema::create('stories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('judul');
                $table->text('isi');
                $table->unsignedInteger('user_id')->nullable();
                $table->string('lokasi', 150)->nullable();
                $table->string('gambar')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nia', 50)->nullable()->unique();
                $table->string('nama', 100);
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->string('angkatan', 50)->nullable();
                $table->enum('status', ['Siswa Aktif', 'Alumni'])->nullable()->default('Siswa Aktif');
                $table->unsignedInteger('divisi_id')->nullable();
                $table->enum('role', ['admin', 'pengurus', 'anggota'])->nullable()->default('anggota');
                $table->string('foto_profil')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        if (DB::table('divisi')->doesntExist()) {
            DB::table('divisi')->insert([
                ['nama_divisi' => 'Hutan Gunung', 'deskripsi' => null, 'logo' => null],
                ['nama_divisi' => 'Climbing', 'deskripsi' => null, 'logo' => null],
                ['nama_divisi' => 'Canoe', 'deskripsi' => null, 'logo' => null],
            ]);
        }
    }

    public function down(): void
    {
        // Legacy application tables may contain imported production data; keep them on rollback.
    }
};
