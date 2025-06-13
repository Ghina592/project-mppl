<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengumumanSekolah; // Pastikan model diimpor
use Carbon\Carbon; // Untuk tanggal

class PengumumanSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PengumumanSekolah::firstOrCreate(
            [
                'description' => 'Libur Kenaikan Kelas Tahun Ajaran 2024/2025',
                'start_at' => Carbon::parse('2025-06-20')->toDateString(),
                'end_at' => Carbon::parse('2025-07-05')->toDateString(),
            ],
            [
                // Jika ada kolom lain yang perlu diisi, tambahkan di sini
            ]
        );

        PengumumanSekolah::firstOrCreate(
            [
                'description' => 'Rapat Orang Tua Murid Kelas X',
                'start_at' => Carbon::parse('2025-07-10')->toDateString(),
                'end_at' => Carbon::parse('2025-07-10')->toDateString(),
            ],
            [
                //
            ]
        );

        // Tambahkan data pengumuman lain sesuai kebutuhan
    }
}