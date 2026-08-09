<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->double('start_latitude')->nullable()->change();
            $table->double('start_longitude')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('attendances')
            ->whereNull('start_latitude')
            ->update(['start_latitude' => DB::raw('schedule_latitude')]);

        DB::table('attendances')
            ->whereNull('start_longitude')
            ->update(['start_longitude' => DB::raw('schedule_longitude')]);

        Schema::table('attendances', function (Blueprint $table) {
            $table->double('start_latitude')->nullable(false)->change();
            $table->double('start_longitude')->nullable(false)->change();
        });
    }
};
