<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi tabel okr_1_partisipasis.
 * Rekaman data partisipasi masyarakat per bulan per tahun.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_1_partisipasis', function (Blueprint $table) {
            $table->id();
            $table->string('month', 20)->comment('Nama bulan, contoh: Januari');
            $table->integer('year');
            $table->integer('total_warga_wajib_lapor')->comment('Total warga yang wajib hadir');
            $table->integer('warga_hadir')->comment('Jumlah warga yang hadir');
            // Kolom ini dihitung otomatis oleh OkrService, disimpan untuk efisiensi query
            $table->decimal('calculated_percentage', 5, 2)->default(0.00);
            $table->timestamps();

            $table->index(['year', 'month']);
            // Satu entri per bulan per tahun
            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_1_partisipasis');
    }
};
