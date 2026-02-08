<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('jam_kerja_per_hari')->default(8);
            $table->integer('hari_kerja_per_minggu')->default(5);
            $table->integer('hari_kerja_per_bulan')->default(21);
            $table->integer('total_jam_per_bulan')->default(173);
            $table->enum('metode_perhitungan_default', ['bulanan', 'harian', 'jam'])->default('bulanan');
            $table->decimal('tunjangan_transport_default', 15, 2)->default(0);
            $table->decimal('tunjangan_makan_default', 15, 2)->default(0);
            $table->decimal('potongan_bpjs_kesehatan_persen', 5, 2)->default(1); // 1% dari gaji
            $table->decimal('potongan_bpjs_ketenagakerjaan_persen', 5, 2)->default(2); // 2% dari gaji
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_settings');
    }
};
