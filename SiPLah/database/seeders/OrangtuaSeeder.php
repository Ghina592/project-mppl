<?php

namespace Database\Seeders;

use App\Models\Orangtua;
use App\Models\Siswa; // Import model Siswa
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrangtuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 4. Orangtua User
        $userOrangtua = User::firstOrCreate(
            ['email' => 'ortu@mail.com'],
            [
                'name' => 'Orangtua Contoh',
                'password' => Hash::make('ortu123'),
                'roles' => 'orangtua',
            ]
        );
        $orangtuaRecord = Orangtua::firstOrCreate(
            ['user_id' => $userOrangtua->id],
            [
                'nama' => $userOrangtua->name,
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Orangtua',
            ]
        );

        // 5. Sinkronkan Siswa dengan Orangtua (Relasi Many-to-Many)
        // Ambil siswa yang sudah ada (diasumsikan SiswaSeeder sudah berjalan)
        $siswaKevin = Siswa::where('nis', '123454321')->first();
        $siswaSiska = Siswa::where('nis', '543212345')->first();

        if ($siswaKevin) {
            $orangtuaRecord->siswas()->syncWithoutDetaching([$siswaKevin->id]);
        } else {
            $this->command->warn('Siswa Kevin tidak ditemukan untuk disinkronkan dengan Orangtua.');
        }

        if ($siswaSiska) {
            $orangtuaRecord->siswas()->syncWithoutDetaching([$siswaSiska->id]);
        } else {
            $this->command->warn('Siswa Siska tidak ditemukan untuk disinkronkan dengan Orangtua.');
        }
    }
}