<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi tabel users.
 * Mendefinisikan semua kolom pengguna dengan role RBAC dan support 2FA.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable();
            $table->enum('role', ['admin', 'kepala_desa', 'tim_monitoring', 'bpd'])->default('tim_monitoring');
            // NIP 13 digit, khusus untuk administrator
            $table->string('nip_administrator', 13)->unique()->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
