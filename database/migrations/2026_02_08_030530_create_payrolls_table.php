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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('periode'); // Format: YYYY-MM (2026-01)
            $table->integer('bulan');
            $table->integer('tahun');
            
            // Data Kehadiran
            $table->integer('total_hari_kerja')->default(0);
            $table->integer('total_hari_hadir')->default(0);
            $table->integer('total_jam_kerja')->default(0);
            $table->integer('total_jam_hadir')->default(0);
            $table->integer('total_terlambat')->default(0); // dalam menit
            
            // Perhitungan Gaji
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('gaji_per_hari', 15, 2)->nullable();
            $table->decimal('gaji_per_jam', 15, 2)->nullable();
            
            // Tunjangan
            $table->decimal('tunjangan_transport', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->decimal('tunjangan_lainnya', 15, 2)->default(0);
            $table->decimal('total_tunjangan', 15, 2)->default(0);
            
            // Potongan
            $table->decimal('potongan_bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('potongan_bpjs_ketenagakerjaan', 15, 2)->default(0);
            $table->decimal('potongan_pph21', 15, 2)->default(0);
            $table->decimal('potongan_keterlambatan', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            
            // Total
            $table->decimal('gaji_kotor', 15, 2); // Gaji Pokok + Tunjangan
            $table->decimal('gaji_bersih', 15, 2); // Gaji Kotor - Potongan
            
            // Status & Approval
            $table->enum('status', ['draft', 'pending', 'approved', 'paid', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
