<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('position');           // e.g. Karyawan, Kepala Divisi
            $table->string('division')->nullable(); // e.g. IT, Marketing
            $table->string('role')->nullable();     // e.g. employee, kepala_divisi
            $table->date('start_date');
            $table->date('end_date')->nullable();   // null = masih aktif
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_histories');
    }
};
