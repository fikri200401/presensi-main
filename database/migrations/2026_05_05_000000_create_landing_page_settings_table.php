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
        Schema::create('landing_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name')->default('HRIS Portal');
            $table->string('brand_subtitle')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('nav_documentation_label')->default('Dokumentasi');
            $table->string('nav_documentation_url')->default('/info/dokumentasi');
            $table->string('nav_support_label')->default('IT Support');
            $table->string('nav_support_url')->default('/info/it-support');
            $table->string('nav_status_label')->default('Status Sistem');
            $table->string('nav_status_url')->default('/info/status-sistem');
            $table->string('hero_badge')->default('PORTAL INTERNAL PERUSAHAAN');
            $table->string('hero_title')->default('Selamat Datang di HRIS Portal Terpadu');
            $table->string('hero_highlight')->default('HRIS Portal');
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_primary_button_label')->default('Masuk ke Dashboard');
            $table->string('hero_secondary_button_label')->default('Panduan Pengguna');
            $table->string('hero_secondary_button_url')->default('/info/panduan');
            $table->string('hero_image')->nullable();
            $table->json('stats')->nullable();
            $table->string('features_title')->default('Navigasi Fitur Portal');
            $table->text('features_description')->nullable();
            $table->json('features')->nullable();
            $table->string('feature_image')->nullable();
            $table->string('cta_title')->default('Siap Memulai Hari Kerja Anda?');
            $table->text('cta_description')->nullable();
            $table->string('cta_primary_button_label')->default('Portal Login');
            $table->string('cta_secondary_button_label')->default('Kontak Admin');
            $table->string('cta_secondary_button_url')->default('/info/it-support');
            $table->text('footer_description')->nullable();
            $table->string('footer_quick_access_title')->default('Quick Access');
            $table->string('footer_support_title')->default('Support');
            $table->string('footer_legal_title')->default('Legal');
            $table->json('footer_links')->nullable();
            $table->string('copyright_text')->default('HRIS Internal Portal. All rights reserved.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_settings');
    }
};
