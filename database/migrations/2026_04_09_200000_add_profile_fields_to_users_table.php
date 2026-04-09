<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('division');
            $table->text('address')->nullable()->after('phone');
            $table->string('position')->nullable()->after('address');
            $table->string('nip', 30)->nullable()->after('position');
            $table->date('birth_date')->nullable()->after('nip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'position', 'nip', 'birth_date']);
        });
    }
};
