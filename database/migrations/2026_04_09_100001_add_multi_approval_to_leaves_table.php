<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Drop old enum status & recreate with new values
            $table->dropColumn('status');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->after('reason');

            // Approval layer 1: Kepala Divisi
            $table->foreignId('approved_by_kadiv')->nullable()->after('note')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at_kadiv')->nullable()->after('approved_by_kadiv');
            $table->text('note_kadiv')->nullable()->after('approved_at_kadiv');

            // Approval layer 2: HR / Admin
            $table->foreignId('approved_by_hr')->nullable()->after('note_kadiv')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at_hr')->nullable()->after('approved_by_hr');
            $table->text('note_hr')->nullable()->after('approved_at_hr');

            // Approval layer 3: Direksi
            $table->foreignId('approved_by_direksi')->nullable()->after('note_hr')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at_direksi')->nullable()->after('approved_by_direksi');
            $table->text('note_direksi')->nullable()->after('approved_at_direksi');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['approved_by_kadiv']);
            $table->dropForeign(['approved_by_hr']);
            $table->dropForeign(['approved_by_direksi']);
            $table->dropColumn([
                'approved_by_kadiv', 'approved_at_kadiv', 'note_kadiv',
                'approved_by_hr', 'approved_at_hr', 'note_hr',
                'approved_by_direksi', 'approved_at_direksi', 'note_direksi',
            ]);
            $table->dropColumn('status');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('reason');
        });
    }
};
