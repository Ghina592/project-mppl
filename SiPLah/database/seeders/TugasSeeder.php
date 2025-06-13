<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tugas;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Guru;

class TugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mapelId = Mapel::first()->id ?? null;
        $kelasId = Kelas::first()->id ?? null;
        $guruId = Guru::first()->id ?? null;

        if (!$mapelId || !$kelasId || !$guruId) {
            $this->command->warn('Peringatan: Dependensi (Mapel, Kelas, atau Guru) tidak ditemukan untuk TugasSeeder. Data Tugas mungkin tidak dibuat.');
            return;
        }

        Tugas::firstOrCreate(
            [
                'judul' => 'Tugas Aljabar Lanjut',
                'guru_id' => $guruId,
                'mapel_id' => $mapelId,
                'kelas_id' => $kelasId,
            ],
            [
                'deskripsi' => 'Kerjakan soal-soal di halaman 50 buku paket.',
                'file' => 'public/tugas/aljabar_lanjut.pdf', // <-- UBAH DARI 'file_path' MENJADI 'file'
                'tanggal_dikumpulkan' => now()->addDays(7),
                'is_aktif' => true,
            ]
        );

        Tugas::firstOrCreate(
            [
                'judul' => 'Tugas Kimia Organik',
                'guru_id' => $guruId,
                'mapel_id' => Mapel::skip(1)->first()->id ?? $mapelId,
                'kelas_id' => $kelasId,
            ],
            [
                'deskripsi' => 'Buat rangkuman tentang hidrokarbon.',
                'file' => null, // <-- UBAH DARI 'file_path' MENJADI 'file'
                'tanggal_dikumpulkan' => now()->addDays(5),
                'is_aktif' => true,
            ]
        );

        // Tambahkan data tugas lain sesuai kebutuhan
    }
}