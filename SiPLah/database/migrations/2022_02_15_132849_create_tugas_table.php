<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('kelas_id')->unsigned();
            $table->bigInteger('guru_id')->unsigned();
            $table->bigInteger('mapel_id')->unsigned()->nullable();

            $table->string('judul');
            $table->string('deskripsi')->nullable();
            $table->string('file')->nullable();
            $table->timestamp('tanggal_batas')->nullable();
            // --- TAMBAHKAN KOLOM INI ---
            $table->timestamp('tanggal_dikumpulkan')->nullable(); // Kolom baru

            $table->boolean('is_aktif')->default(true);

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tugas');
    }
}
