<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;   // Menggunakan model Kelas
use App\Models\Jurusan; // Menggunakan model Jurusan
use App\Models\Guru;    // Menggunakan model Guru

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan Jurusan dan Guru sudah ada di database sebelum KelasSeeder dijalankan.
        // Ini dijamin dengan urutan pemanggilan di DatabaseSeeder.php.

        // Ambil ID Jurusan secara dinamis berdasarkan namanya
        $ipaJurusanId = Jurusan::where('nama_jurusan', 'IPA')->first()->id;
        $ipsJurusanId = Jurusan::where('nama_jurusan', 'IPS')->first()->id;

        // Ambil ID Guru secara dinamis
        // Ambil guru pertama (ID 1 jika auto-increment dimulai dari 1)
        $guru1Id = Guru::first()->id;
        // Ambil guru kedua (jika ada)
        $guru2Id = Guru::skip(1)->first()->id ?? $guru1Id; // Fallback ke guru1Id jika hanya ada 1 guru

        // Tambahkan pengecekan jika ID tidak ditemukan (meskipun seharusnya tidak dengan urutan seeder yang benar)
        if (is_null($ipaJurusanId) || is_null($ipsJurusanId) || is_null($guru1Id)) {
            $this->command->warn('Peringatan: Data Jurusan atau Guru tidak ditemukan. Seeder Kelas mungkin tidak lengkap.');
            return; // Hentikan eksekusi seeder ini jika data dasar tidak ada
        }

        // Gunakan Eloquent Model untuk insert data, lebih Laravel-way
        Kelas::create([
            'nama_kelas' => 'X IPA 1',
            'jurusan_id' => $ipaJurusanId,
            'guru_id' => $guru1Id,
        ]);

        Kelas::create([
            'nama_kelas' => 'X IPS 1',
            'jurusan_id' => $ipsJurusanId,
            'guru_id' => $guru2Id, // Menggunakan guru kedua, atau fallback ke guru pertama
        ]);

        // Anda bisa menambahkan kelas lain di sini
        // Kelas::create([
        //     'nama_kelas' => 'XI IPA 1',
        //     'jurusan_id' => $ipaJurusanId,
        //     'guru_id' => $guru1Id,
        // ]);
    }
}