<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->decimal('tunjangan_jabatan_kepala_divisi', 15, 2)->default(0)->after('tunjangan_makan_default');
            $table->decimal('potongan_jht_persen', 5, 2)->default(0)->after('potongan_bpjs_ketenagakerjaan_persen');
            $table->decimal('potongan_jks_persen', 5, 2)->default(0)->after('potongan_jht_persen');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('tunjangan_jabatan', 15, 2)->default(0)->after('tunjangan_makan');
            $table->decimal('potongan_jht', 15, 2)->default(0)->after('potongan_bpjs_ketenagakerjaan');
            $table->decimal('potongan_jks', 15, 2)->default(0)->after('potongan_jht');
            $table->decimal('potongan_manual', 15, 2)->default(0)->after('potongan_keterlambatan');
            $table->text('keterangan_potongan_manual')->nullable()->after('potongan_manual');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'tunjangan_jabatan',
                'potongan_jht',
                'potongan_jks',
                'potongan_manual',
                'keterangan_potongan_manual',
            ]);
        });

        Schema::table('salary_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tunjangan_jabatan_kepala_divisi',
                'potongan_jht_persen',
                'potongan_jks_persen',
            ]);
        });
    }
};
