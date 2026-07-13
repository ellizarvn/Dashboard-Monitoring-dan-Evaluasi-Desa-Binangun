<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Okr2BumdesUnit;
use App\Models\Okr1Partisipasi;
use App\Models\Okr2Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OkrImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_mutator_role_cannot_import_okr(): void
    {
        $bpd = User::factory()->create([
            'role' => 'admin',
            'jabatan' => 'BPD',
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('test.csv', 100);

        $this->actingAs($bpd)
            ->post(route('okr1.import'), ['file_import' => $file])
            ->assertStatus(403);

        $this->actingAs($bpd)
            ->post(route('okr2.import'), ['file_import' => $file])
            ->assertStatus(403);
    }

    public function test_admin_can_import_okr1_partisipasi(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'jabatan' => 'Administrator',
            'is_active' => true,
        ]);

        $csvContent = "Bulan;Tahun;Warga Wajib Lapor;Warga Hadir\n" .
                      "Januari;2026;200;180\n" .
                      "Februari;2026;200;190\n";

        $file = UploadedFile::fake()->createWithContent('okr1.csv', $csvContent);

        $response = $this->actingAs($admin)
            ->post(route('okr1.import'), ['file_import' => $file]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('okr_1_partisipasis', [
            'month' => 'Januari',
            'year' => 2026,
            'total_warga_wajib_lapor' => 200,
            'warga_hadir' => 180,
        ]);
    }

    public function test_admin_can_import_okr2_transactions(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'jabatan' => 'Administrator',
            'is_active' => true,
        ]);

        $unit = Okr2BumdesUnit::create([
            'name_unit' => 'Unit Toko BUMDes',
            'pic_name' => 'John Doe',
            'sector' => 'Perdagangan',
            'initial_capital' => 10000000,
        ]);

        $csvContent = "Nama Unit;Tanggal Transaksi;Jenis;Nominal;Keterangan\n" .
                      "Unit Toko BUMDes;2026-05-15;Pemasukan;5000000;Penjualan pupuk\n" .
                      "Unit Toko BUMDes;2026-05-16;Pengeluaran;1500000;Biaya cetak\n";

        $file = UploadedFile::fake()->createWithContent('okr2.csv', $csvContent);

        $response = $this->actingAs($admin)
            ->post(route('okr2.import'), ['file_import' => $file]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('okr_2_transactions', [
            'bumdes_unit_id' => $unit->id,
            'type' => 'Pemasukan',
            'nominal' => 5000000.00,
        ]);
    }
}
