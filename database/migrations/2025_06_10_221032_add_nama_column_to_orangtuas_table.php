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
        Schema::table('orangtuas', function (Blueprint $table) {
            // Ini adalah BARIS PENTING untuk mengatasi error "Field 'name' doesn't have a default value".
            // Baris ini akan mengubah kolom 'name' yang sudah ada menjadi nullable (bisa kosong).
            $table->string('name')->nullable()->change();

            // Ini adalah penambahan kolom 'nama' yang Anda inginkan
            $table->string('nama')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orangtuas', function (Blueprint $table) {
            // Hapus kolom 'nama' yang baru ditambahkan saat rollback
            $table->dropColumn('nama');

            // Opsional: Jika Anda ingin mengembalikan kolom 'name' ke NOT NULL saat rollback
            // Ini hanya relevan jika di 'up()' Anda membuatnya nullable dan ingin mengembalikannya.
            // Jika kolom 'name' tidak pernah ada atau sudah nullable dari awal, baris ini tidak perlu.
            // $table->string('name')->nullable(false)->change();
        });
    }
};