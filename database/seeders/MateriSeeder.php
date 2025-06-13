<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materi;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Guru; // <-- PENTING: Import model Guru, bukan User
use Illuminate\Support\Facades\Hash; // Jika diperlukan

class MateriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil ID Mapel, Kelas, dan Guru yang sudah ada
        // Pastikan seeder Jurusan, Mapel, User (yang membuat Guru), dan Kelas sudah berjalan terlebih dahulu
        $mapelId = Mapel::first()->id ?? null;
        $kelasId = Kelas::first()->id ?? null;
        $guruRecord = Guru::first(); // Ambil objek Guru pertama
        $guruId = $guruRecord->id ?? null; // Dapatkan ID-nya

        if (!$mapelId) {
            $this->command->warn('Peringatan: Mapel tidak ditemukan untuk MateriSeeder. Data Materi tidak akan dibuat.');
            return;
        }
        if (!$kelasId) {
            $this->command->warn('Peringatan: Kelas tidak ditemukan untuk MateriSeeder. Data Materi tidak akan dibuat.');
            return;
        }
        if (!$guruId) {
            $this->command->error('Error: Guru tidak ditemukan untuk MateriSeeder. Pastikan UserSeeder membuat setidaknya satu guru.');
            return; // Hentikan jika guru tidak ada
        }


        // Materi 1
        Materi::firstOrCreate(
            [
                'judul' => 'Pengenalan Aljabar',
                'guru_id' => $guruId, // <-- UBAH INI: Gunakan guru_id
            ],
            [
                'deskripsi' => 'Materi dasar aljabar untuk siswa kelas X.',
                'file' => 'public/materi/aljabar_dasar.pdf', // <-- UBAH INI: Sesuaikan dengan nama kolom di migrasi ('file')
                'mapel_id' => $mapelId,
                'kelas_id' => $kelasId,
                // 'is_aktif' => true, // Hapus ini jika kolom is_aktif tidak ada di migrasi
            ]
        );

        // Materi 2
        Materi::firstOrCreate(
            [
                'judul' => 'Struktur Atom',
                'guru_id' => $guruId, // <-- UBAH INI: Gunakan guru_id
            ],
            [
                'deskripsi' => 'Penjelasan mengenai struktur dasar atom dan partikel penyusunnya.',
                'file' => 'public/materi/struktur_atom.pdf', // <-- UBAH INI: Sesuaikan dengan nama kolom di migrasi ('file')
                'mapel_id' => $mapelId,
                'kelas_id' => $kelasId,
                // 'is_aktif' => true, // Hapus ini jika kolom is_aktif tidak ada di migrasi
            ]
        );
    }
}