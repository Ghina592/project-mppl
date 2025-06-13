<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Siswa;   // Import model Siswa
use App\Models\Mapel;   // Import model Mapel (sesuai nama tabel 'mapels')
use App\Models\Guru;    // Import model Guru
use App\Models\Kelas;   // Import model Kelas
use Illuminate\Http\Request;
use Carbon\Carbon;      // Untuk bekerja dengan tanggal dan waktu

class PresensiController extends Controller
{
    /**
     * Menampilkan form untuk input presensi siswa.
     * Memungkinkan pemilihan kelas dan tanggal untuk memfilter daftar siswa.
     * Juga memuat data presensi yang sudah ada untuk tanggal dan kelas yang dipilih.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Ambil semua data kelas dan mata pelajaran untuk dropdown filter
        $kelas = Kelas::all();
        $mapels = Mapel::all();

        // Ambil kelas dan tanggal yang dipilih dari request, atau set default
        $selectedKelas = $request->input('kelas_id');
        $selectedDate = $request->input('tanggal', Carbon::today()->toDateString()); // Default ke tanggal hari ini

        $siswas = collect(); // Inisialisasi koleksi siswa kosong
        if ($selectedKelas) {
            // Jika kelas dipilih, ambil daftar siswa di kelas tersebut, diurutkan berdasarkan nama
            $siswas = Siswa::where('kelas_id', $selectedKelas)->orderBy('nama')->get();
        }

        $presensiData = []; // Array untuk menyimpan data presensi yang sudah ada
        if ($selectedKelas && $siswas->isNotEmpty()) {
            // Jika kelas dipilih dan ada siswa, ambil presensi yang sudah ada untuk tanggal dan siswa tersebut
            $existingPresensis = Presensi::where('tanggal_presensi', $selectedDate)
                                        ->whereIn('siswa_id', $siswas->pluck('id')) // Ambil presensi hanya untuk siswa di kelas yang dipilih
                                        ->get()
                                        ->keyBy('siswa_id'); // Kunci koleksi berdasarkan siswa_id agar mudah diakses

            // Isi array presensiData dengan data presensi yang sudah ada atau null
            foreach ($siswas as $siswa) {
                $presensiData[$siswa->id] = $existingPresensis->get($siswa->id);
            }
        }

        // Kirim data ke view
        return view('presensi.create', compact('kelas', 'mapels', 'selectedKelas', 'selectedDate', 'siswas', 'presensiData'));
    }

    /**
     * Menyimpan atau memperbarui data presensi siswa.
     * Menerima input dari form presensi (status, jam masuk/keluar, keterangan untuk setiap siswa).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'tanggal_presensi' => 'required|date',
            'siswa.*.status_presensi' => 'required|in:Hadir,Absen,Izin,Sakit', // Validasi status setiap siswa
            'siswa.*.keterangan' => 'nullable|string|max:255',
            'siswa.*.jam_masuk' => 'nullable|date_format:H:i',
            'siswa.*.jam_keluar' => 'nullable|date_format:H:i',
            // 'siswa.*.mata_pelajaran_id' => 'nullable|exists:mapels,id', // Jika presensi per mapel, validasi juga mapel_id
        ]);

        $tanggalPresensi = $request->input('tanggal_presensi');
        // Ambil ID guru yang sedang login (asumsi user yang login memiliki relasi hasOne ke model Guru)
        // Jika user yang mencatat presensi adalah Admin, Anda mungkin ingin menanganinya berbeda.
        $guruId = auth()->user()->guru->id ?? null; // Null jika user bukan guru atau relasi tidak ditemukan

        // Loop melalui data siswa yang dikirim dari form
        foreach ($request->input('siswa') as $siswaId => $data) {
            // Menggunakan updateOrCreate:
            // Akan mencari record berdasarkan kondisi pertama (siswa_id, tanggal_presensi, mata_pelajaran_id)
            // Jika ditemukan, akan diupdate dengan data di array kedua.
            // Jika tidak ditemukan, akan membuat record baru dengan data di kedua array.
            Presensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal_presensi' => $tanggalPresensi,
                    // Tambahkan ini jika presensi per mata pelajaran.
                    // Jika tidak, hapus baris ini dan juga di UNIQUE index tabel DB.
                    'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                ],
                [
                    'status_presensi' => $data['status_presensi'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'jam_masuk' => $data['jam_masuk'] ?? null,
                    'jam_keluar' => $data['jam_keluar'] ?? null,
                    'guru_id' => $guruId, // Simpan ID guru yang mencatat
                    // 'updated_at' akan otomatis diperbarui oleh Laravel
                ]
            );
        }

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Presensi berhasil disimpan.');
    }

    /**
     * Menampilkan daftar semua catatan presensi.
     * Hanya untuk tampilan admin/guru untuk melihat riwayat presensi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data presensi, dengan eager loading relasi terkait
        // (siswa, kelas siswa, mapel, guru) untuk menghindari N+1 problem.
        $presensis = Presensi::with('siswa.kelas', 'mapel', 'guru')
                             ->orderBy('tanggal_presensi', 'desc') // Urutkan berdasarkan tanggal terbaru
                             ->paginate(20); // Gunakan pagination untuk data besar

        // Kirim data ke view
        return view('presensi.index', compact('presensis'));
    }
}