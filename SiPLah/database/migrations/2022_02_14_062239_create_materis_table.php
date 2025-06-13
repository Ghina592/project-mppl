<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('deskripsi')->nullable();
            $table->string('file')->nullable(); // Nama kolom file
            $table->bigInteger('guru_id')->unsigned(); // Ini sudah ada di migrasi Anda
            $table->bigInteger('kelas_id')->unsigned(); // Ini sudah ada di migrasi Anda

            // --- TAMBAHKAN BARIS INI UNTUK mapel_id ---
            $table->bigInteger('mapel_id')->unsigned(); // Tambahkan kolom mapel_id
            // ------------------------------------------

            $table->timestamps();

            // Relation Tables (foreign key definitions)
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            // --- TAMBAHKAN FOREIGN KEY INI UNTUK mapel_id ---
            $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('cascade');
            // Opsi lain jika mapel bisa null: $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('set null');
            // -----------------------------------------------
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materis');
    }
}