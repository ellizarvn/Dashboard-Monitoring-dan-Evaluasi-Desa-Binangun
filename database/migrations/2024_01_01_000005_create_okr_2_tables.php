<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi tabel okr_2_bumdes_units.
 * Master data unit usaha BUMDes beserta sektor dan penanggung jawab.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_2_bumdes_units', function (Blueprint $table) {
            $table->id();
            $table->string('name_unit')->comment('Nama unit usaha BUMDes');
            $table->enum('sector', ['Perdagangan', 'Pertanian', 'Pariwisata', 'Jasa']);
            $table->string('pic_name')->comment('Nama penanggung jawab unit');
            $table->decimal('initial_capital', 15, 2)->comment('Modal awal unit usaha (Rupiah)');
            $table->timestamps();

            $table->index('sector');
        });

        Schema::create('okr_2_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bumdes_unit_id')
                  ->constrained('okr_2_bumdes_units')
                  ->onDelete('cascade');
            $table->date('transaction_date');
            $table->enum('type', ['Pemasukan', 'Pengeluaran']);
            $table->decimal('nominal', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['bumdes_unit_id', 'transaction_date']);
            $table->index('type');
        });

        Schema::create('okr_2_pades_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bumdes_unit_id')
                  ->constrained('okr_2_bumdes_units')
                  ->onDelete('cascade');
            // Format YYYY-MM, contoh: 2024-07
            $table->string('period_year_month', 7)->comment('Periode setoran: YYYY-MM');
            $table->decimal('nominal_setoran', 15, 2)->comment('Nominal setoran ke PADes (Rupiah)');
            $table->string('file_proof_path')->nullable()->comment('Path file bukti transfer');
            $table->timestamps();

            $table->index(['bumdes_unit_id', 'period_year_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_2_pades_contributions');
        Schema::dropIfExists('okr_2_transactions');
        Schema::dropIfExists('okr_2_bumdes_units');
    }
};
