<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas; // Import model Kelas
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil ID Kelas yang sudah ada (diasumsikan KelasSeeder sudah berjalan)
        $kelasId1 = Kelas::first()->id ?? null;
        $kelasId2 = Kelas::skip(1)->first()->id ?? null;

        if (is_null($kelasId1) || is_null($kelasId2)) {
            $this->command->warn('Tidak cukup data Kelas untuk SiswaSeeder. Pastikan KelasSeeder dijalankan dan memiliki setidaknya 2 entri.');
            return;
        }

        // Siswa Users
        $userKevin = User::firstOrCreate(
            ['email' => 'kevin@mail.com'],
            [
                'name' => 'Kevin Hartanto',
                'password' => Hash::make('kevin123'),
                'roles' => 'siswa',
            ]
        );
        Siswa::firstOrCreate(
            ['user_id' => $userKevin->id],
            [
                'nama' => $userKevin->name,
                'nis' => '123454321',
                'kelas_id' => $kelasId1, // Menggunakan ID dinamis
                'telp' => '081234567893',
                'alamat' => 'Jl. Siswa No. 1',
            ]
        );

        $userSiska = User::firstOrCreate(
            ['email' => 'siska@mail.com'],
            [
                'name' => 'Siska Saraswati',
                'password' => Hash::make('siska123'),
                'roles' => 'siswa',
            ]
        );
        Siswa::firstOrCreate(
            ['user_id' => $userSiska->id],
            [
                'nama' => $userSiska->name,
                'nis' => '543212345',
                'kelas_id' => $kelasId2, // Menggunakan ID dinamis
                'telp' => '081234567894',
                'alamat' => 'Jl. Siswa No. 2',
            ]
        );
    }
}