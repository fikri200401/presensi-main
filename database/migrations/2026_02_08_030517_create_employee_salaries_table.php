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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('gaji_pokok_bulanan', 15, 2);
            $table->decimal('gaji_per_jam', 15, 2)->nullable();
            $table->decimal('gaji_per_hari', 15, 2)->nullable();
            $table->enum('tipe_karyawan', ['tetap', 'harian', 'paruh_waktu'])->default('tetap');
            $table->enum('metode_perhitungan', ['bulanan', 'harian', 'jam'])->default('bulanan');
            
            // Tunjangan
            $table->decimal('tunjangan_transport', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->decimal('tunjangan_lainnya', 15, 2)->default(0);
            $table->text('keterangan_tunjangan')->nullable();
            
            // Potongan
            $table->decimal('potongan_bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('potongan_bpjs_ketenagakerjaan', 15, 2)->default(0);
            $table->decimal('potongan_pph21', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->text('keterangan_potongan')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->date('berlaku_dari')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};
