<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jawaban;
use App\Models\Siswa;
use App\Models\Tugas;

class JawabanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $siswaId = Siswa::first()->id ?? null;
        $tugasId = Tugas::first()->id ?? null;

        if (!$siswaId || !$tugasId) {
            $this->command->warn('Peringatan: Dependensi (Siswa atau Tugas) tidak ditemukan untuk JawabanSeeder. Data Jawaban mungkin tidak dibuat.');
            return;
        }

        Jawaban::firstOrCreate(
            [
                'tugas_id' => $tugasId,
                'siswa_id' => $siswaId,
            ],
            [
                // --- PERBAIKI / SESUAIKAN KOLOM-KOLOM INI ---
                'file_path' => 'public/jawaban/jawaban_aljabar.pdf', // <-- Pastikan ini sesuai dengan nama kolom di migrasi
                'nilai' => 90, // Contoh nilai
                'catatan_guru' => 'Jawaban sangat baik, terus tingkatkan!',
                // Jika ada kolom 'jawaban' untuk teks, dan Anda ingin mengisinya:
                // 'jawaban' => 'Ini adalah jawaban teks untuk tugas aljabar.',
                // --------------------------------------------
            ]
        );

        // Anda bisa menambahkan lebih banyak data jawaban
    }
}