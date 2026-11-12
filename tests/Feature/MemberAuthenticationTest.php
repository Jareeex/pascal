<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_sign_in_with_nia_and_password(): void
    {
        User::create([
            'nama' => 'Anggota Tes',
            'email' => 'anggota@example.test',
            'nia' => 'P-100',
            'password' => Hash::make('password-aman'),
            'role' => 'anggota',
        ]);

        $this->post('/login', ['login_input' => 'P-100', 'password' => 'password-aman'])
            ->assertRedirect(route('members.profile'));

        $this->assertAuthenticated();
    }

    public function test_application_form_saves_a_pending_application(): void
    {
        DB::table('divisi')->insert(['nama_divisi' => 'Hutan Gunung']);

        $this->post('/pendaftaran', [
            'nama_lengkap' => 'Calon Anggota',
            'email' => 'calon@example.test',
            'nomor_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'divisi_minat' => 'Hutan Gunung',
        ])->assertRedirect(route('applications.create'));

        $this->assertDatabaseHas('pendaftaran', [
            'email' => 'calon@example.test',
            'status' => 'pending',
        ]);
    }

    public function test_admin_accepting_an_application_creates_a_member_account(): void
    {
        $admin = User::create([
            'nama' => 'Admin Tes',
            'email' => 'admin@example.test',
            'password' => Hash::make('password-aman'),
            'role' => 'admin',
        ]);
        $applicationId = DB::table('pendaftaran')->insertGetId([
            'nama_lengkap' => 'Anggota Baru',
            'email' => 'baru@example.test',
            'nomor_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'divisi_minat' => 'Hutan Gunung',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch('/admin/pendaftaran/'.$applicationId, ['status' => 'diterima'])
            ->assertRedirect()
            ->assertSessionHas('temporary_password');

        $this->assertDatabaseHas('users', [
            'email' => 'baru@example.test',
            'nama' => 'Anggota Baru',
            'role' => 'anggota',
        ]);
        $this->assertDatabaseHas('pendaftaran', ['id' => $applicationId, 'status' => 'diterima']);
    }

    public function test_admin_pages_require_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }
}
