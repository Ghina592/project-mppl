<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\Orangtua;
use App\Models\PengumumanSekolah;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Tugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Redirect berdasarkan peran pengguna yang login
        if (Auth::user()->roles == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->roles == 'guru') {
            return redirect()->route('guru.dashboard');
        } elseif (Auth::user()->roles == 'siswa') {
            return redirect()->route('siswa.dashboard');
        } elseif (Auth::user()->roles == 'orangtua') {
            return redirect()->route('orangtua.dashboard');
        } else {
            // Peran tidak dikenal atau default, bisa diarahkan ke halaman login
            Auth::logout();
            return redirect('/login')->with('error', 'Peran pengguna tidak dikenali.');
        }
    }

    /**
     * Menampilkan dashboard untuk peran Admin.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function admin()
    {
        $siswa = Siswa::count();
        $guru = Guru::count();
        $kelas = Kelas::count();
        $mapel = Mapel::count();
        // Mengambil 5 siswa terbaru (mungkin 'id' di sini maksudnya 'created_at' atau primary key terbaru)
        $siswaBaru = Siswa::orderByDesc('id')->take(5)->get(); // Menggunakan get() untuk mengambil koleksi

        return view('pages.admin.dashboard', compact('siswa', 'guru', 'kelas', 'mapel', 'siswaBaru'));
    }

    /**
     * Menampilkan dashboard untuk peran Guru.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function guru()
    {
        // Temukan objek Guru yang terkait dengan user yang sedang login
        $guru = Guru::where('user_id', Auth::user()->id)->first();

        // Jika objek guru tidak ditemukan, tangani kasusnya (misal: redirect atau error)
        if (is_null($guru)) {
            Log::error('User ID ' . Auth::id() . ' dengan peran guru tidak memiliki record Guru terkait.');
            Auth::logout(); // Opsional: paksa logout untuk user yang tidak valid
            return redirect('/login')->with('error', 'Profil guru Anda tidak ditemukan. Silakan hubungi administrator.');
        }

        $materi = Materi::where('guru_id', $guru->id)->count();

        // --- Perbaikan dimulai di sini ---
        // Mengambil nama hari dalam Bahasa Indonesia (misal: "Senin")
        $hariUntukView = Carbon::now()->locale('id')->isoFormat('dddd');
        // Konversi ke huruf kecil untuk perbandingan di database (misal: "senin")
        $hariFilter = strtolower($hariUntukView);

        // Ambil jadwal yang terkait dengan mapel_id guru, DAN sesuai hari ini
        // Eager load relasi 'mapel' dan 'kelas' untuk tampilan yang lengkap
        $jadwal = Jadwal::with('mapel', 'kelas') // Tambahkan eager load 'kelas'
                        ->where('mapel_id', $guru->mapel_id)
                        ->where('hari', $hariFilter) // PENTING: Filter berdasarkan hari ini
                        ->orderBy('dari_jam', 'asc') // Urutkan berdasarkan jam
                        ->get();
        // --- Perbaikan berakhir di sini ---

        $tugas = Tugas::where('guru_id', $guru->id)->count();

        // Menggunakan $hariUntukView untuk konsistensi di view
        return view('pages.guru.dashboard', compact('guru', 'materi', 'jadwal', 'hariUntukView', 'tugas'));
    }

    /**
     * Menampilkan dashboard untuk peran Siswa.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function siswa()
    {
        // Muat user yang sedang login dan eager load relasi siswa dan kelas dari siswa
        // Pastikan relasi 'siswa' ada di model User, dan 'kelas' ada di model Siswa.
        $user = Auth::user()->load('siswa.kelas');
        $siswa = $user->siswa;

        // Selalu ambil pengumuman, karena ini tidak bergantung pada $siswa atau $kelas
        $pengumumans = PengumumanSekolah::active()->get();

        // Tangani jika user siswa tidak memiliki record Siswa terkait
        if (is_null($siswa)) {
            Log::error('User ID ' . Auth::id() . ' dengan peran siswa tidak memiliki record Siswa terkait.');
            Auth::logout(); // Opsional: paksa logout untuk user yang tidak valid
            return redirect('/login')->with('error', 'Profil siswa Anda tidak ditemukan. Silakan hubungi administrator.');
        }

        // Ambil objek Kelas terkait dari objek Siswa
        $kelas = $siswa->kelas;

        // Inisialisasi variabel dengan koleksi kosong atau nilai default
        // Ini memastikan variabel selalu terdefinisi meskipun $kelas null
        $materi = collect();
        $tugas = collect();
        $jadwal = collect();
        $hariUntukView = Carbon::now()->locale('id')->isoFormat('dddd'); // e.g., "Senin"
        $hariFilter = strtolower($hariUntukView); // e.g., "senin"

        // Debugging Awal (Uncomment untuk melihat data awal):
        // dd($siswa, $kelas);

        // Tangani jika Siswa tidak memiliki Kelas yang terkait
        if (is_null($kelas)) {
            Log::warning('Siswa ID ' . $siswa->id . ' tidak memiliki record Kelas terkait (kelas_id mungkin NULL atau tidak valid).');

            return view('pages.siswa.dashboard', compact('siswa', 'pengumumans', 'materi', 'tugas', 'jadwal', 'hariUntukView'))
                                 ->with('kelas_tidak_ditemukan', true)
                                 ->with('error_message', 'Anda belum memiliki kelas yang ditugaskan. Mohon hubungi administrator.');
        }

        // Jika $siswa dan $kelas tidak null, lanjutkan perhitungan data dashboard
        $materi = Materi::where('kelas_id', $kelas->id)->limit(3)->get();
        $tugas = Tugas::where('kelas_id', $kelas->id)->limit(3)->get();

        // Ambil jadwal berdasarkan kelas siswa DAN HARI INI
        // Pastikan kolom 'hari' di database tersimpan dalam format huruf kecil (e.g., 'senin', 'selasa')
        $jadwal = Jadwal::with('mapel') // Eager load mapel untuk menampilkan nama mapel
                         ->where('kelas_id', $kelas->id)
                         ->where('hari', $hariFilter) // PENTING: Filter berdasarkan hari ini
                         ->orderBy('dari_jam', 'asc') // Urutkan berdasarkan jam
                         ->get();

        // Debugging Final (Uncomment untuk memeriksa data yang akan dikirim ke view):
        // dd([
        //     'user_id' => $user->id,
        //     'siswa_id' => $siswa->id,
        //     'kelas_id' => $kelas->id,
        //     'nama_kelas' => $kelas->nama_kelas,
        //     'hariUntukView' => $hariUntukView,
        //     'hariFilterUntukQuery' => $hariFilter,
        //     'jadwalHariIni' => $jadwal->toArray(),
        //     // 'semuaJadwalDiDB' => Jadwal::all()->toArray() // Opsional: Untuk perbandingan jika jadwal kosong
        // ]);


        return view('pages.siswa.dashboard', compact('materi', 'siswa', 'kelas', 'tugas', 'jadwal', 'hariUntukView', 'pengumumans'));
    }

    /**
     * Menampilkan dashboard untuk peran Orangtua.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function orangtua()
    {
        // Eager load relasi siswas dan kelas dari siswas
        // Pastikan relasi 'siswas' ada di model Orangtua dan 'kelas' di model Siswa.
        $orangtua = Orangtua::with('siswas.kelas')
            ->where('user_id', Auth::user()->id)
            ->first();
        $pengumumans = PengumumanSekolah::active()->get();

        // Debugging (Uncomment jika Anda perlu memeriksa data orangtua):
        // dd($orangtua->toArray());

        return view('pages.orangtua.dashboard', compact('orangtua', 'pengumumans'));
    }
}