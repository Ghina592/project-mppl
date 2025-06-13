<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed data dasar yang tidak memiliki dependensi atau dependensi fundamental
        $this->call(JurusanSeeder::class); // Jurusan harus ada pertama

        // 2. Seed Mapel (tergantung pada Jurusan)
        $this->call(MapelSeeder::class);

        // 3. Seed User dan Guru (Guru tergantung pada Mapel)
        $this->call(UserSeeder::class);

        // 4. Seed Kelas (Kelas tergantung pada Jurusan dan Guru)
        $this->call(KelasSeeder::class);

        // 5. Seed Siswa (Siswa tergantung pada User dan Kelas)
        $this->call(SiswaSeeder::class);

        // 6. Seed Orangtua (Orangtua tergantung pada User dan Siswa)
        $this->call(OrangtuaSeeder::class);

        // 7. Seed data lain yang memiliki ketergantungan pada entitas di atas (User, Guru, Siswa, Kelas, Mapel, Jurusan)
        $this->call(JadwalSeeder::class);             // Tergantung Guru, Kelas, Mapel
        $this->call(MateriSeeder::class);             // Tergantung Guru, Mapel, Kelas
        $this->call(TugasSeeder::class);              // Tergantung Guru, Mapel, Kelas
        $this->call(JawabanSeeder::class);            // Tergantung Siswa, Tugas

        // 8. Seeder tambahan yang umumnya independen atau memiliki dependensi sederhana
        $this->call(PengumumanSekolahSeeder::class);
        $this->call(PengaturanSeeder::class);
    }
}