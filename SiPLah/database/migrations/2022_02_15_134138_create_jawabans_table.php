<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jawabans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_id');
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('guru_id')->nullable(); // Tambahkan guru_id jika Anda menyimpannya di sini
                                                              // Tambahkan nullable jika tidak selalu ada

            // Perhatikan nama kolom ini sesuai dengan model Jawaban dan tempat Anda menyimpan file/teks
            // Jika 'file' adalah nama kolom untuk path file di model Jawaban:
            $table->string('file')->nullable(); // Untuk path file jawaban (sesuai model Jawaban)
            // Jika ada kolom terpisah untuk teks jawaban:
            $table->text('jawaban')->nullable(); // Untuk jawaban dalam bentuk teks

            $table->integer('nilai')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamp('tanggal_kumpul')->nullable(); // Tambahkan kolom ini untuk tanggal kumpul

            $table->timestamps();

            // Relation tables - Pastikan onDelete('cascade') di sini
            $table->foreign('tugas_id')->references('id')->on('tugas')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('set null'); // Opsional: set null jika guru dihapus
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jawabans');
    }
}
