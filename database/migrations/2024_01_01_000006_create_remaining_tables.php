<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi tabel-tabel sisa:
 * - okr_3_sdm_capacities: Data kapasitas SDM perangkat desa
 * - village_programs: Program-program desa aktif
 * - reports: Laporan dokumen desa
 * - system_logs: Audit trail aktivitas sistem
 */
return new class extends Migration
{
    public function up(): void
    {
        // OKR 3: Kapasitas SDM Perangkat Desa
        Schema::create('okr_3_sdm_capacities', function (Blueprint $table) {
            $table->id();
            $table->date('period_date')->comment('Tanggal periode pencatatan');
            $table->integer('total_perangkat')->comment('Total perangkat desa aktif');
            $table->integer('total_staf_terlatih')->comment('Jumlah staf yang telah mengikuti pelatihan');
            $table->decimal('avg_competency_score', 3, 1)->comment('Rata-rata skor kompetensi (0.0 - 10.0)');
            $table->decimal('keaktifan_kinerja_persen', 5, 2)->comment('Persentase keaktifan kinerja (%)');
            $table->timestamps();

            $table->index('period_date');
        });

        // Program-Program Desa
        Schema::create('village_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama program desa');
            $table->string('linked_okr', 50)->comment('OKR terkait: OKR1/OKR2/OKR3');
            $table->enum('status', ['AKTIF', 'PENDING', 'SELESAI'])->default('PENDING');
            $table->integer('progress_percentage')->default(0)->comment('Progress 0-100%');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('linked_okr');
        });

        // Laporan Desa
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 100)->comment('Jenis laporan: Bulanan, Triwulan, Tahunan, dll');
            $table->date('report_date');
            $table->foreignId('author_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('status', ['Draft', 'Published'])->default('Draft');
            $table->timestamps();

            $table->index(['status', 'report_date']);
            $table->index('author_id');
        });

        // Audit Trail & System Logs
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->enum('activity_type', [
                'UPDATE DATA',
                'LOGIN',
                'LOGOUT',
                'UNAUTHORIZED ACCESS',
                'UPLOAD FILE',
                'DELETE DATA',
                'CREATE DATA',
                'EXPORT DATA',
            ]);
            $table->string('module', 100)->comment('Modul yang diakses: Dashboard, OKR1, dll');
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['BERHASIL', 'GAGAL'])->default('BERHASIL');
            // Hanya created_at karena log tidak pernah diupdate
            $table->timestamp('created_at')->useCurrent();

            $table->index(['activity_type', 'created_at']);
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('village_programs');
        Schema::dropIfExists('okr_3_sdm_capacities');
    }
};
