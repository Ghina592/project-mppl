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
        Schema::create('orangtua_siswas', function (Blueprint $table) {
            // Untuk tabel pivot, id() tidak selalu diperlukan,
            // seringkali cukup dengan composite primary key dari foreign keys
            // $table->id(); // Anda bisa biarkan ini jika ingin ID unik untuk setiap relasi
            $table->foreignId('orangtua_id')->references('id')->on('orangtuas')->onDelete('cascade');
            $table->foreignId('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->timestamps();

            // Opsional: Menambahkan composite primary key untuk mencegah duplikasi relasi
            $table->primary(['orangtua_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orangtua_siswas');
    }
};
