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
        Schema::create('presensis', function (Blueprint $table) { // <-- UBAH 'Stable' menjadi '$table'
            $table->id(); // <-- UBAH 'Stable->id()' menjadi '$table->id()'
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade'); // <-- UBAH 'Stable->foreignId()' menjadi '$table->foreignId()'

            // Tambahkan semua kolom lain yang Anda butuhkan untuk tabel presensi
            // Sesuai dengan model Presensi Anda, tambahkan ini:
            $table->date('tanggal_presensi');
            $table->string('status_presensi'); // Contoh: Hadir, Absen, Izin, Sakit
            $table->text('keterangan')->nullable();
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();

            // Untuk foreign key mata_pelajaran_id dan guru_id, pastikan nama tabelnya benar.
            // Jika modelnya Mapel -> tabelnya mapels
            // Jika modelnya Guru -> tabelnya gurus
            $table->unsignedBigInteger('mata_pelajaran_id')->nullable();
            $table->foreign('mata_pelajaran_id')->references('id')->on('mapels')->onDelete('set null'); // Sesuaikan 'mapels'
            $table->unsignedBigInteger('guru_id')->nullable();
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('set null'); // Sesuaikan 'gurus'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};