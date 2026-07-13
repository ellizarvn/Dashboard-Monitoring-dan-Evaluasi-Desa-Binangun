<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah struktur tabel
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
            $table->string('jabatan')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('photo');
        });

        // 2. Migrasikan data role lama ke kolom jabatan
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $jabatan = match ($user->role) {
                'admin'          => 'Administrator',
                'kepala_desa'    => 'Kepala Desa',
                'tim_monitoring' => 'Tim Monitoring',
                'bpd'            => 'BPD',
                default          => 'Tim Monitoring',
            };

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role'    => 'admin',
                    'jabatan' => $jabatan,
                ]);
        }

        // 3. Masukkan default Super Admin
        DB::table('users')->insert([
            'name'               => 'Super Admin Binangun',
            'email'              => 'superadmin@desabinangun.id',
            'password'           => Hash::make('SuperAdmin@2026!'),
            'role'               => 'super_admin',
            'jabatan'            => null,
            'is_active'          => true,
            'two_factor_enabled' => false,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus Super Admin bawaan
        DB::table('users')->where('email', 'superadmin@desabinangun.id')->delete();

        // Kembalikan data jabatan ke role lama sebelum menghapus kolom
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $oldRole = match ($user->jabatan) {
                'Administrator'  => 'admin',
                'Kepala Desa'    => 'kepala_desa',
                'Tim Monitoring' => 'tim_monitoring',
                'BPD'            => 'bpd',
                default          => 'tim_monitoring',
            };

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role' => $oldRole,
                ]);
        }

        // Kembalikan struktur tabel ke semula
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jabatan');
            $table->dropColumn('is_active');
        });
    }
};
