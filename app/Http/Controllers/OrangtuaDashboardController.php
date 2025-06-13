<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Orangtua; // Pastikan nama model Anda 'Orangtua'
use App\Models\Siswa;    // Pastikan nama model Anda 'Siswa'
use App\Models\Presensi;
use App\Models\PengumumanSekolah; // Import model PengumumanSekolah
use Carbon\Carbon; // Import Carbon untuk bekerja dengan tanggal

class OrangtuaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // User yang sedang login

        // Ambil data orang tua yang terkait dengan user ini
        $orangtua = $user->orangtua; // Asumsi relasi hasOne di User model

        $presensis = collect(); // Default koleksi presensi kosong
        // Ambil pengumuman yang aktif hari ini
        $pengumumans = PengumumanSekolah::where('start_at', '<=', Carbon::today())
                                        ->where('end_at', '>=', Carbon::today())
                                        ->get();


        if (!$orangtua) {
            // Jika profil orang tua tidak ditemukan
            return view('pages.orangtua.dashboard', [ // JALUR VIEW DIPERBAIKI DI SINI
                'message' => 'Profil orang tua tidak ditemukan.',
                'presensis' => collect(), // Kirim koleksi kosong untuk menghindari error
                'orangtua' => (object)['siswas' => collect()], // Kirim objek kosong untuk menghindari error di view
                'pengumumans' => $pengumumans,
            ]);
        }

        // Ambil semua siswa yang terkait dengan orang tua ini
        $siswasId = $orangtua->siswas->pluck('id')->toArray();

        if (empty($siswasId)) {
            // Jika tidak ada anak yang terdaftar
            return view('pages.orangtua.dashboard', [ // JALUR VIEW DIPERBAIKI DI SINI
                'message' => 'Tidak ada anak yang terdaftar di bawah akun Anda.',
                'presensis' => collect(), // Kirim koleksi kosong untuk menghindari error
                'orangtua' => $orangtua, // Tetap kirim objek orangtua yang valid
                'pengumumans' => $pengumumans,
            ]);
        }

        // Ambil data presensi untuk semua siswa yang terkait
        $presensis = Presensi::whereIn('siswa_id', $siswasId)
                            ->with('siswa.kelas', 'mapel', 'guru') // Eager load relasi
                            ->orderBy('tanggal_presensi', 'desc')
                            ->get();

        // Kirim semua data yang dibutuhkan ke view
        return view('pages.orangtua.dashboard', compact('presensis', 'orangtua', 'pengumumans')); // JALUR VIEW DIPERBAIKI DI SINI
    }
}