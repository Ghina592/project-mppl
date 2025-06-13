<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Mapel; // Import model Mapel
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil ID Mapel yang sudah ada (diasumsikan MapelSeeder sudah berjalan)
        $mapelId1 = Mapel::first()->id ?? null; // Ambil ID mapel pertama
        $mapelId2 = Mapel::skip(1)->first()->id ?? null; // Ambil ID mapel kedua (jika ada)

        // Peringatan jika data dependensi tidak cukup
        if (is_null($mapelId1) || is_null($mapelId2)) {
            $this->command->warn('Tidak cukup data Mapel untuk UserSeeder (Guru). Pastikan MapelSeeder dijalankan dan memiliki setidaknya 2 entri.');
            // Hentikan eksekusi seeder ini jika Mapel tidak cukup
            return;
        }

        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'roles' => 'admin',
            ]
        );

        // 2. Guru Users
        $userBudi = User::firstOrCreate(
            ['email' => 'budi@mail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('budi123'),
                'roles' => 'guru',
            ]
        );
        Guru::firstOrCreate(
            ['user_id' => $userBudi->id],
            [
                'nama' => $userBudi->name,
                'nip' => '1234567890',
                'mapel_id' => $mapelId1, // Menggunakan ID dinamis
                'no_telp' => '081234567891',
                'alamat' => 'Jl. Guru No. 1',
            ]
        );

        $userGunawan = User::firstOrCreate(
            ['email' => 'gunawan@mail.com'],
            [
                'name' => 'Gunawan Efendi',
                'password' => Hash::make('gunawan123'),
                'roles' => 'guru',
            ]
        );
        Guru::firstOrCreate(
            ['user_id' => $userGunawan->id],
            [
                'nama' => $userGunawan->name,
                'nip' => '0987654321',
                'mapel_id' => $mapelId2, // Menggunakan ID dinamis
                'no_telp' => '081234567892',
                'alamat' => 'Jl. Guru No. 2',
            ]
        );

        // *** HAPUS BAGIAN PEMBUATAN SISWA DAN ORANGTUA DARI SINI ***
        // Mereka akan dipindahkan ke SiswaSeeder dan OrangtuaSeeder
    }
}