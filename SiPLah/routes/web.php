<?php

// Import semua Controllers yang digunakan
use App\Http\Controllers\GuruController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengumumanSekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\OrangtuaDashboardController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rute utama (homepage), memerlukan autentikasi
Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

// Rute autentikasi Laravel (login, register, reset password, dll.)
Auth::routes();

// Rute dashboard utama setelah login (default Laravel, biasanya mengarah ke HomeController)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Grup Rute untuk Pengguna yang Sudah Login (Middleware 'auth')
// Mencakup fungsionalitas umum seperti profil dan perubahan password
Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', [UserController::class, 'edit'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'update'])->name('update.profile');
    Route::get('/edit-password', [UserController::class, 'editPassword'])->name('ubah-password');
    Route::patch('/update-password', [UserController::class, 'updatePassword'])->name('update-password');

    // Rute umum untuk download jawaban siswa
    // Detail otorisasi (siapa yang boleh download jawaban siapa) ditangani di TugasController@downloadJawaban
    // INI ADALAH RUTE YANG SEHARUSNYA TERDAFTAR SEBAGAI 'jawaban.download'
    Route::get('/jawaban/download/{id}', [TugasController::class, 'downloadJawaban'])->name('jawaban.download');
});

// Grup Rute Khusus untuk Role Guru (Middleware 'checkRole:guru')
Route::group(['middleware' => ['auth', 'checkRole:guru']], function () {
    Route::get('/guru/dashboard', [HomeController::class, 'guru'])->name('guru.dashboard');

    // Resource routes untuk Materi dan Tugas (ini akan membuat GET, POST, PUT/PATCH, DELETE)
    Route::resource('materi', MateriController::class);
    Route::resource('tugas', TugasController::class);

    // Rute khusus untuk download jawaban tugas oleh guru DIHAPUS karena sudah digeneralisasi di atas
    // Route::get('/jawaban-download/{id}', [TugasController::class, 'downloadJawaban'])->name('guru.jawaban.download');

    // Rute untuk Presensi (bagi guru)
    Route::get('/presensi/create', [PresensiController::class, 'create'])->name('presensi.create');
    Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index'); // Melihat daftar presensi
});

// Grup Rute Khusus untuk Role Siswa (Middleware 'checkRole:siswa')
Route::group(['middleware' => ['auth', 'checkRole:siswa']], function () {
    Route::get('/siswa/dashboard', [HomeController::class, 'siswa'])->name('siswa.dashboard');

    // Rute materi dan tugas bagi siswa
    Route::get('/siswa/materi', [MateriController::class, 'siswa'])->name('siswa.materi');
    Route::get('/materi-download/{id}', [MateriController::class, 'download'])->name('siswa.materi.download');

    // Rute ini adalah untuk menampilkan daftar tugas siswa
    Route::get('/siswa/tugas', [TugasController::class, 'siswa'])->name('siswa.tugas');

    // Rute untuk download file tugas/soal oleh siswa
    Route::get('/siswa/tugas-download/{id}', [TugasController::class, 'download'])->name('siswa.tugas.download');

    // Rute untuk menampilkan formulir pengumpulan tugas (GET request)
    // Nama rute sudah sesuai dengan pemanggilan di view
    Route::get('/siswa/tugas/{tugas}/kumpul-form', [TugasController::class, 'showKumpulForm'])->name('siswa.tugas.kumpul_form');

    // Rute untuk memproses pengumpulan tugas (POST request)
    // Nama rute sudah sesuai dengan pemanggilan di view
    Route::post('/siswa/tugas/{tugas}/kumpul', [TugasController::class, 'kumpulTugas'])->name('siswa.tugas.kumpul');
});

// Grup Rute Khusus untuk Role Orang Tua (Middleware 'checkRole:orangtua')
Route::group(['middleware' => ['auth', 'checkRole:orangtua']], function () {
    // Dashboard khusus orang tua
    Route::get('/orangtua/dashboard', [OrangtuaDashboardController::class, 'index'])->name('orangtua.dashboard');

    // Rute untuk melihat tugas siswa oleh orang tua
    Route::get('/orangtua/tugas/siswa', [TugasController::class, 'orangtua'])->name('orangtua.tugas.siswa');
    Route::get('/orangtua/tugas-anak', [App\Http\Controllers\TugasAnakController::class, 'index'])->name('orangtua.tugas-anak');
});

// Grup Rute Khusus untuk Role Admin (Middleware 'checkRole:admin')
// PENTING: Perhatikan penambahan 'prefix' dan 'as' di sini
Route::group(['middleware' => ['auth', 'checkRole:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', [HomeController::class, 'admin'])->name('dashboard'); // Nama rute akan menjadi 'admin.dashboard'

    // Resource routes untuk manajemen data
    Route::resource('jurusan', JurusanController::class); // Nama rute: admin.jurusan.index, admin.jurusan.store, dll.
    Route::resource('mapel', MapelController::class);     // Nama rute: admin.mapel.index, admin.mapel.store, dll.
    Route::resource('guru', GuruController::class);       // Rute ini sudah otomatis membuat rute DELETE: admin.guru.destroy
    Route::resource('kelas', KelasController::class);     // Nama rute: admin.kelas.index, admin.kelas.store, dll.

    // Ini adalah rute SISWA yang menjadi fokus kita
    // Nama rute akan otomatis menjadi: admin.siswa.index, admin.siswa.store, admin.siswa.show, dll.
    Route::resource('siswa', SiswaController::class);

    Route::resource('user', UserController::class);       // Nama rute: admin.user.index, admin.user.store, dll.
    Route::resource('jadwal', JadwalController::class);   // Nama rute: admin.jadwal.index, admin.jadwal.store, dll.
    Route::resource('pengumuman-sekolah', PengumumanSekolahController::class); // Nama rute: admin.pengumuman-sekolah.index, dll.
    Route::resource('pengaturan', PengaturanController::class); // Nama rute: admin.pengaturan.index, dll.
});
