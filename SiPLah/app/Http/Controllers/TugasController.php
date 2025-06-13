<?php

namespace App\Http\Controllers; // DIPERBAIKI: Menggunakan '\' untuk namespace

use App\Models\Guru; // DIPERBAIKI: Menggunakan '\' untuk namespace
use App\Models\Jawaban; // DIPERBAIKI: Menggunakan '\' untuk namespace
use App\Models\Kelas; // DIPERBAIKI: Menggunakan '\' untuk namespace
use App\Models\Siswa; // DIPERBAIKI: Menggunakan '\' untuk namespace
use App\Models\Tugas; // DIPERBAIKI: Menggunakan '\' untuk namespace
use App\Models\Orangtua; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Http\Request; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Support\Facades\Storage; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Support\Facades\Response; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Support\Facades\Log; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Support\Facades\DB; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Illuminate\Database\QueryException; // DIPERBAIKI: Menggunakan '\' untuk namespace
use Carbon\Carbon;

// Pastikan model-model yang terkait diimpor jika digunakan
// use App\Models\Penilaian; // Contoh, jika ada tabel penilaian
// use App\Models\KomentarTugas; // Contoh, jika ada tabel komentar tugas

class TugasController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware otorisasi.
     * Ini adalah praktik terbaik untuk mengelola izin berdasarkan peran.
     */
    public function __construct()
    {
        // Middleware 'auth' untuk semua metode yang memerlukan login
        $this->middleware('auth');

        // Middleware 'checkRole' untuk membatasi akses berdasarkan peran
        // PASTIKAN NAMA ALIAS ADALAH 'checkRole' SESUAI KERNEL.PHP
        // Dan 'roles' (plural) di tabel 'users' untuk data peran
        // Jika Anda menggunakan Spatie Laravel Permission, ganti 'checkRole' dengan 'role'
        // dan periksa metode hasRole() di middleware CheckRole Anda.

        // Hanya guru yang bisa mengakses fitur manajemen tugas guru
        $this->middleware('checkRole:guru')->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Hanya siswa yang bisa mengakses fitur terkait tugas siswa (melihat, mengumpulkan)
        $this->middleware('checkRole:siswa')->only(['siswa', 'showKumpulForm', 'kumpulTugas']);

        // Hanya orang tua yang bisa mengakses fitur terkait tugas orang tua
        $this->middleware('checkRole:orangtua')->only(['orangtua']);

        // Middleware untuk metode download yang mungkin diakses oleh beberapa role.
        // Otorisasi lebih lanjut akan ditangani di dalam masing-masing metode download.
        // Tidak perlu mendeklarasikan ini di sini jika sudah ada di group route umum yang diautentikasi.
        // Contoh: $this->middleware('checkRole:guru,siswa,orangtua')->only('downloadJawaban');
    }

    /**
     * Display a listing of the resource.
     * Untuk Guru: Menampilkan daftar tugas yang dibuat oleh guru yang sedang login.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah guru
        // Menggunakan Auth::user()->roles karena kolom di DB adalah 'roles' (plural)
        if (Auth::user()->roles !== 'guru') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $guru = Guru::where('user_id', Auth::user()->id)->first();
        if (is_null($guru)) {
            Log::error('TugasController@index: User ID ' . Auth::id() . ' dengan peran guru tidak memiliki record Guru terkait.');
            Auth::logout();
            return redirect('/login')->with('error', 'Profil guru Anda tidak ditemukan. Silakan hubungi administrator.');
        }

        $tugas = Tugas::with(['kelas', 'guru.mapel'])
                      ->where('guru_id', $guru->id)
                      ->orderBy('created_at', 'desc')
                      ->get();

        $kelas = Kelas::all();

        return view('pages.guru.tugas.index', compact('tugas', 'guru', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah guru
        if (Auth::user()->roles !== 'guru') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        $kelas = Kelas::all();
        return view('pages.guru.tugas.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah guru
        if (Auth::user()->roles !== 'guru') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses untuk membuat tugas.');
        }

        $guru = Guru::where('user_id', Auth::user()->id)->first();
        if (is_null($guru)) {
            // Ini akan memicu redirect()->back() jika guru tidak ditemukan
            Log::error('TugasController@store: Data guru Anda tidak ditemukan untuk User ID ' . Auth::id() . '. Tugas tidak dapat dibuat.');
            return redirect()->back()->with('error', 'Data guru Anda tidak ditemukan. Tugas tidak dapat dibuat.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_batas' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
            'file' => 'nullable|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
        ], [
            'tanggal_batas.required' => 'Tanggal batas wajib diisi.',
            'tanggal_batas.date_format' => 'Format tanggal batas tidak valid.',
            'tanggal_batas.after_or_equal' => 'Tanggal batas harus hari ini atau setelahnya.'
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('file/tugas', $namaFile, 'public');
        }

        $tugas = new Tugas;
        $tugas->guru_id = $guru->id; // Pastikan ini diisi dengan ID guru yang valid
        if (!is_null($guru->mapel_id)) {
            $tugas->mapel_id = $guru->mapel_id;
        } else {
            Log::warning('TugasController@store: Guru ID ' . $guru->id . ' tidak memiliki mapel_id. Tugas akan disimpan tanpa mapel_id.');
        }
        $tugas->kelas_id = $request->kelas_id;
        $tugas->judul = $request->judul;
        $tugas->deskripsi = $request->deskripsi;
        $tugas->tanggal_batas = $request->tanggal_batas;
        $tugas->file = $filePath;
        $tugas->save();

        // Tambahkan log untuk konfirmasi guru_id yang disimpan
        Log::info('TugasController@store: Tugas ID ' . $tugas->id . ' berhasil ditambahkan. Guru ID yang disimpan: ' . $tugas->guru_id . ' (dari profil guru ID ' . $guru->id . ').');

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah guru
        if (Auth::user()->roles !== 'guru') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $tugas = Tugas::with(['kelas', 'guru.mapel'])->find($id);

        if (!$tugas) {
            return back()->with('error', 'Tugas tidak ditemukan.');
        }

        // Lapisan keamanan ketiga: Pastikan guru hanya bisa melihat tugas yang dibuatnya
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        if (is_null($guru) || $tugas->guru_id !== $guru->id) {
            return redirect()->route('tugas.index')->with('error', 'Anda tidak memiliki izin untuk melihat detail tugas ini.');
        }

        // MENGAMBIL SEMUA JAWABAN UNTUK TUGAS INI
        // Memuat relasi 'siswa' pada setiap jawaban
        $jawaban = Jawaban::with('siswa.kelas')
                          ->where('tugas_id', $tugas->id)
                          ->orderBy('created_at', 'desc')
                          ->get();

        return view('pages.guru.tugas.show', compact('tugas', 'jawaban')); // Mengirimkan variabel 'jawaban' ke view
    }

    /**
     * Show the form for editing the specified resource.
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah guru
        if (Auth::user()->roles !== 'guru') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('TugasController@edit: Gagal mendekripsi ID tugas: ' . $id . '. Error: ' . $e->getMessage());
            return back()->with('error', 'ID tugas tidak valid.');
        }

        $tugas = Tugas::find($decryptedId);
        if (!$tugas) {
            return back()->with('error', 'Tugas tidak ditemukan.');
        }

        // Lapisan keamanan ketiga: Pastikan guru hanya bisa mengedit tugas yang dibuatnya
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        if (is_null($guru) || $tugas->guru_id !== $guru->id) {
            return redirect()->route('tugas.index')->with('error', 'Anda tidak memiliki izin untuk mengedit tugas ini.');
        }

        $kelas = Kelas::all();
        return view('pages.guru.tugas.edit', compact('tugas', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah guru
        if (Auth::user()->roles !== 'guru') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses untuk memperbarui tugas.');
        }

        $tugas = Tugas::find($id);
        if (is_null($tugas)) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan untuk diperbarui.');
        }

        // Lapisan keamanan ketiga: Pastikan guru hanya bisa memperbarui tugas yang dibuatnya
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        if (is_null($guru) || $tugas->guru_id !== $guru->id) {
            return redirect()->route('tugas.index')->with('error', 'Anda tidak memiliki izin untuk memperbarui tugas ini.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_batas' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
            'file' => 'nullable|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
        ], [
            'tanggal_batas.required' => 'Tanggal batas wajib diisi.',
            'tanggal_batas.date_format' => 'Format tanggal batas tidak valid.',
            'tanggal_batas.after_or_equal' => 'Tanggal batas harus hari ini atau setelahnya.'
        ]);

        $tugas->judul = $request->judul;
        $tugas->deskripsi = $request->deskripsi;
        $tugas->kelas_id = $request->kelas_id;
        $tugas->tanggal_batas = $request->tanggal_batas;

        // Guru ID sudah seharusnya sama, tapi untuk memastikan
        $tugas->guru_id = $guru->id;

        if (!is_null($guru->mapel_id)) {
            $tugas->mapel_id = $guru->mapel_id;
        } else {
            Log::warning('TugasController@update: Guru ID ' . $guru->id . ' tidak memiliki mapel_id saat update. Tugas akan diupdate tanpa mapel_id.');
        }

        if ($request->hasFile('file')) {
            if ($tugas->file && Storage::disk('public')->exists($tugas->file)) {
                Storage::disk('public')->delete($tugas->file);
            }
            $file = $request->file('file');
            $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('file/tugas', $namaFile, 'public');
            $tugas->file = $filePath;
        }

        $tugas->save();

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     * @param  \App\Models\Tugas $tugas Menggunakan Route Model Binding
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tugas $tugas) // Menggunakan Route Model Binding untuk tugas
    {
        Log::info('DELETE_INIT: Permintaan penghapusan diterima untuk Tugas ID: ' . ($tugas->id ?? 'unknown'));
        Log::debug('DELETE_DEBUG: Objek Tugas yang diterima: ' . json_encode($tugas->toArray()));

        $loggedInUser = Auth::user();
        Log::debug('DELETE_AUTH_DEBUG: User yang login ID: ' . ($loggedInUser->id ?? 'N/A') . ', Role: ' . ($loggedInUser->roles ?? 'N/A'));

        // Lapisan keamanan kedua: Pastikan hanya guru yang login
        if ($loggedInUser->roles !== 'guru') {
            Log::warning('DELETE_AUTH_FAIL: User ID ' . ($loggedInUser->id ?? 'N/A') . ' mencoba menghapus tugas. Role bukan guru.');
            return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus tugas ini. Hanya guru yang dapat menghapus tugas.'], 403);
        }

        $guru = Guru::where('user_id', Auth::user()->id)->first(); // Menggunakan Auth::user()->id secara konsisten

        // --- DEBUGGING LOGS UNTUK MASALAH GURU TIDAK BISA HAPUS ---
        // Log ini akan memberikan nilai-nilai yang dibandingkan
        Log::debug('DELETE_AUTH_DEBUG: Perbandingan Guru ID dan Tugas Guru ID:');
        Log::debug('DELETE_AUTH_DEBUG: User ID Login: ' . ($loggedInUser->id ?? 'N/A'));
        Log::debug('DELETE_AUTH_DEBUG: Guru Profil Ditemukan (dari users.id): ' . ($guru ? 'Ya, ID: ' . $guru->id . ', User ID: ' . ($guru->user_id ?? 'N/A') : 'Tidak ditemukan (NULL)'));
        Log::debug('DELETE_AUTH_DEBUG: Tugas ID yang akan dihapus: ' . ($tugas->id ?? 'N/A'));
        Log::debug('DELETE_AUTH_DEBUG: Tugas Guru ID (dari tabel tugas): ' . ($tugas->guru_id ?? 'N/A')); // Ini adalah nilai yang kemungkinan NULL/N/A
        // --- END DEBUGGING LOGS ---

        // PERBAIKAN UTAMA: Pengecekan eksplisit jika guru_id pada tugas adalah NULL
        // Ini akan memberikan pesan error yang lebih spesifik jika terjadi masalah data guru_id
        if (is_null($tugas->guru_id)) {
            Log::error('DELETE_AUTH_ERROR: Tugas->guru_id adalah NULL untuk Tugas ID ' . ($tugas->id ?? 'N/A') . '. Tugas ini tidak memiliki guru pemilik yang valid.');
            return response()->json([
                'message' => 'Gagal menghapus tugas. Tugas ini tidak memiliki guru pemilik yang terdaftar. Mohon hubungi administrator.'
            ], 403); // Status 403 (Forbidden) atau 409 (Conflict) bisa digunakan
        }

        // Lapisan keamanan ketiga: Validasi kepemilikan tugas
        // Ini adalah kondisi yang sebelumnya gagal karena guru_id tugas adalah NULL atau tidak cocok
        if (is_null($guru) || $tugas->guru_id !== $guru->id) {
            Log::warning('DELETE_AUTH_FAIL: Guru ID ' . ($guru->id ?? 'N/A') . ' mencoba menghapus tugas ID ' . $tugas->id . ' yang bukan miliknya.');
            return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus tugas ini. Tugas ini bukan Anda yang membuat.'], 403);
        }

        // --- Jika semua otorisasi berhasil, lanjutkan penghapusan ---
        DB::beginTransaction();

        try {
            // 1. Hapus file tugas terkait
            if ($tugas->file && Storage::disk('public')->exists($tugas->file)) {
                Log::debug('DELETE_FILE_TUGAS: Mencoba menghapus file tugas: ' . $tugas->file);
                Storage::disk('public')->delete($tugas->file);
                Log::info('DELETE_FILE_TUGAS: File tugas terkait Tugas ID ' . $tugas->id . ' berhasil dihapus.');
            } else {
                Log::info('DELETE_FILE_TUGAS: Tidak ada file tugas atau file tidak ditemukan untuk Tugas ID ' . $tugas->id . '.');
            }

            // 2. Hapus semua file jawaban siswa terkait dan recordnya
            // Penting: Lakukan ini sebelum menghapus tugas utama untuk menghindari foreign key constraint
            $jawabanTerkait = Jawaban::where('tugas_id', $tugas->id)->get();
            foreach ($jawabanTerkait as $jawaban) {
                if ($jawaban->file && Storage::disk('public')->exists($jawaban->file)) {
                    Storage::disk('public')->delete($jawaban->file);
                }
                $jawaban->delete(); // Hapus record jawaban dari database
            }
            Log::info('DELETE_JAWABAN_FILES: File dan record jawaban siswa terkait Tugas ID ' . $tugas->id . ' (jika ada) berhasil dihapus.');

            // 3. Hapus data di tabel lain yang mungkin memiliki foreign key ke tugas_id
            //    INI ADALAH BAGIAN KRITIS UNTUK MENGATASI ERROR "OPERASI DELETE() MENGEMBALIKAN FALSE"
            //    Anda perlu menambahkan baris ini untuk setiap model/tabel yang memiliki foreign key 'tugas_id'
            //    dan tidak diatur ON DELETE CASCADE di database.
            //    Contoh: Jika Anda punya model Penilaian atau KomentarTugas yang terhubung ke Tugas
            //    Penilaian::where('tugas_id', $tugas->id)->delete(); // Pastikan Anda mengimpor model ini di atas
            //    KomentarTugas::where('tugas_id', $tugas->id)->delete(); // Pastikan Anda mengimpor model ini di atas

            // 4. Hapus record tugas utama
            Log::debug('DELETE_DB: Sebelum memanggil $tugas->delete() untuk Tugas ID: ' . $tugas->id);
            $isDeleted = $tugas->delete();

            Log::debug('DELETE_DB: Hasil $tugas->delete() untuk Tugas ID ' . $tugas->id . ': ' . ($isDeleted ? 'TRUE' : 'FALSE'));

            if ($isDeleted) {
                DB::commit();
                Log::info('DELETE_SUCCESS: Tugas dengan ID: ' . $tugas->id . ' berhasil dihapus dari database.');
                return response()->json(['message' => 'Tugas berhasil dihapus!'], 200); // Mengembalikan JSON sukses
            } else {
                DB::rollBack();
                Log::warning('DELETE_FAILED: Gagal menghapus Tugas ID ' . $tugas->id . '. Operasi delete() mengembalikan FALSE (Mungkin foreign key constraint tidak terdeteksi oleh QueryException, atau model event).');
                return response()->json(['message' => 'Gagal menghapus tugas. Operasi delete() mengembalikan FALSE. Periksa log server untuk detail lebih lanjut.'], 500); // Mengembalikan JSON error
            }

        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == '23000') { // Error kode untuk integrity constraint violation
                Log::error('DELETE_QUERY_EXCEPTION: Foreign key constraint error for Tugas ID ' . $tugas->id . ': ' . $e->getMessage());
                return response()->json([
                    'message' => 'Gagal menghapus tugas. Tugas ini masih memiliki data terkait yang mencegah penghapusan. Mohon hapus data terkait terlebih dahulu atau periksa log untuk detail.'
                ], 409); // Conflict
            }
            Log::error('DELETE_QUERY_EXCEPTION: Error database menghapus Tugas ID ' . $tugas->id . ': ' . $e->getMessage() . ' di ' . $e->getFile() . ' baris ' . $e->getLine(), ['exception' => $e]);
            return response()->json([
                'message' => 'Terjadi kesalahan database saat menghapus data tugas: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DELETE_GENERAL_EXCEPTION: Error umum menghapus Tugas ID ' . ($tugas->id ?? 'N/A') . ': ' . $e->getMessage() . ' di ' . $e->getFile() . ' baris ' . $e->getLine(), ['exception' => $e]);
            return response()->json([
                'message' => 'Terjadi kesalahan umum saat menghapus data tugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan daftar tugas yang relevan untuk peran Siswa.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function siswa()
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah siswa
        if (Auth::user()->roles !== 'siswa') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = Auth::user()->load('siswa.kelas');
        $siswa = $user->siswa;

        if (is_null($siswa)) {
            Log::error('TugasController@siswa: User ID ' . Auth::id() . ' dengan peran siswa tidak memiliki record Siswa terkait.');
            Auth::logout();
            return redirect('/login')->with('error', 'Profil siswa Anda tidak ditemukan. Silakan hubungi administrator.');
        }

        $kelas = $siswa->kelas;
        if (is_null($kelas)) {
            Log::warning('TugasController@siswa: Siswa ID ' . $siswa->id . ' tidak memiliki record Kelas terkait.');
            return view('pages.siswa.tugas.index', compact('siswa'))
                                 ->with('error_message', 'Anda belum memiliki kelas yang ditugaskan untuk melihat tugas.');
        }

        $tugas = Tugas::with(['guru.mapel'])
                             ->where('kelas_id', $kelas->id)
                             ->orderBy('created_at', 'desc')
                             ->get();

        $jawaban = Jawaban::where('siswa_id', $siswa->id)->get()->keyBy('tugas_id');

        $guruKelas = null;
        if (!is_null($kelas->guru_id)) {
            $guruKelas = Guru::find($kelas->guru_id);
        }

        if (is_null($guruKelas)) {
            Log::warning('TugasController@siswa: Kelas ID ' . $kelas->id . ' tidak memiliki guru terkait (guru_id mungkin NULL atau tidak valid).');
            $guruKelas = (object)['nama' => 'Tidak Ada Guru', 'mapel' => (object)['nama_mapel' => '-']];
        }

        return view('pages.siswa.tugas.index', compact('tugas', 'guruKelas', 'kelas', 'jawaban'));
    }

    /**
     * Mengunduh file tugas yang diunggah oleh guru.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        // Akses download file tugas bisa untuk guru, siswa, atau orangtua (sesuai kebutuhan Anda)
        // Jika Anda ingin membatasi, tambahkan pengecekan role di sini:
        // if (!in_array(Auth::user()->roles, ['guru', 'siswa', 'orangtua'])) {
        //     abort(403, 'Anda tidak memiliki izin untuk mengunduh file ini.');
        // }

        $tugas = Tugas::findOrFail($id);

        // Optional: Anda bisa menambahkan validasi bahwa tugas ini milik kelas siswa/orangtua yang login
        // if (Auth::user()->roles === 'siswa' && Auth::user()->siswa->kelas_id !== $tugas->kelas_id) {
        //     abort(403, 'File ini bukan untuk kelas Anda.');
        // }

        if (!$tugas->file || !Storage::disk('public')->exists($tugas->file)) {
            abort(404, 'File tugas tidak ditemukan atau telah dihapus.');
        }

        $path = Storage::disk('public')->path($tugas->file);
        $fileName = basename($tugas->file);
        return Response::download($path, $fileName);
    }

    /**
     * Menampilkan formulir pengumpulan tugas untuk siswa.
     * @param \App\Models\Tugas $tugas
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showKumpulForm(Tugas $tugas)
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah siswa
        if (Auth::user()->roles !== 'siswa') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = Auth::user();
        if ($user && !$user->relationLoaded('siswa')) {
            $user->load('siswa');
        }

        if (!$user || !($user->siswa)) {
            Log::warning('TugasController@showKumpulForm: User ID ' . ($user->id ?? 'unknown') . ' mencoba mengakses form kumpul tugas tanpa profil siswa.');
            return redirect()->route('siswa.tugas')->with('error', 'Profil siswa tidak ditemukan untuk mengakses formulir ini.');
        }

        // Pastikan tugas ini untuk kelas siswa yang sedang login
        if ($tugas->kelas_id !== $user->siswa->kelas_id) {
            Log::warning('TugasController@showKumpulForm: Siswa ID ' . $user->siswa->id . ' mencoba mengakses tugas ID ' . $tugas->id . ' yang bukan untuk kelasnya.');
            return redirect()->route('siswa.tugas')->with('error', 'Tugas ini tidak tersedia untuk kelas Anda.');
        }

        $submission = Jawaban::where('tugas_id', $tugas->id)
                             ->where('siswa_id', $user->siswa->id)
                             ->first();

        return view('pages.siswa.tugas.kirim', compact('tugas', 'submission'));
    }

    /**
     * Mengirimkan jawaban tugas oleh siswa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tugas $tugas Menggunakan Route Model Binding untuk tugas
     * @return \Illuminate\Http\Response
     */
    public function kumpulTugas(Request $request, Tugas $tugas)
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah siswa
        if (Auth::user()->roles !== 'siswa') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengumpulkan tugas.');
        }

        $siswa = Siswa::where('user_id', Auth::user()->id)->first();
        if (is_null($siswa)) {
            Log::error('TugasController@kumpulTugas: User ID ' . Auth::id() . ' dengan peran siswa tidak memiliki record Siswa terkait.');
            return redirect()->back()->with('error', 'Data siswa Anda tidak ditemukan.');
        }

        // Pastikan tugas ini untuk kelas siswa yang sedang login
        if ($tugas->kelas_id !== $siswa->kelas_id) {
            Log::warning('TugasController@kumpulTugas: Siswa ID ' . $siswa->id . ' mencoba mengumpulkan tugas ID ' . $tugas->id . ' yang bukan untuk kelasnya.');
            return redirect()->route('siswa.tugas')->with('error', 'Anda tidak dapat mengumpulkan tugas ini karena bukan untuk kelas Anda.');
        }

        $request->validate([
            'file_tugas' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:5120',
            'catatan_siswa' => 'nullable|string|max:1000',
        ]);

        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file');
            $namaFile = time() . '_' . $siswa->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('tugas_siswa', $namaFile, 'public');
            Log::info('TugasController@kumpulTugas: File berhasil diunggah ke: ' . $filePath);
        } else {
             Log::warning('TugasController@kumpulTugas: Tidak ada file tugas yang diunggah meskipun required.');
             return redirect()->back()->with('error', 'Harap unggah file tugas.');
        }

        $jawaban = Jawaban::where('tugas_id', $tugas->id)
                             ->where('siswa_id', $siswa->id)
                             ->first();

        if ($jawaban) {
            Log::info('TugasController@kumpulTugas: Memperbarui jawaban tugas untuk Siswa ID: ' . $siswa->id . ' Tugas ID: ' . $tugas->id);
            if ($jawaban->file && Storage::disk('public')->exists($jawaban->file)) {
                Storage::disk('public')->delete($jawaban->file);
                Log::info('TugasController@kumpulTugas: File lama dihapus: ' . $jawaban->file);
            }

            $jawaban->file = $filePath;
            $jawaban->jawaban = $request->catatan_siswa;
            $jawaban->tanggal_kumpul = Carbon::now();
            $jawaban->save();
            return redirect()->back()->with('success', 'Jawaban tugas berhasil diperbarui!');
        } else {
            Log::info('TugasController@kumpulTugas: Membuat jawaban tugas baru untuk Siswa ID: ' . $siswa->id . ' Tugas ID: ' . $tugas->id);
            if (is_null($tugas->guru_id)) {
                Log::warning('TugasController@kumpulTugas: Tugas ID ' . $tugas->id . ' tidak memiliki guru_id. Tidak dapat menyimpan jawaban.');
                return redirect()->back()->with('error', 'Tugas ini tidak memiliki guru yang ditugaskan. Silakan hubungi administrator.');
            }

            Jawaban::create([
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
                'guru_id' => $tugas->guru_id,
                'jawaban' => $request->catatan_siswa,
                'file' => $filePath,
                'tanggal_kumpul' => Carbon::now(),
            ]);

            return redirect()->back()->with('success', 'Jawaban tugas berhasil dikumpulkan!');
        }
    }

    /**
     * Mengunduh file jawaban siswa.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadJawaban($id)
    {
        $user = Auth::user();
        $jawaban = Jawaban::findOrFail($id);

        $allowed = false; // Inisialisasi status izin

        // Mengambil profil terkait (Guru/Siswa) secara langsung untuk keandalan
        // dan memeriksa apakah user memiliki profil terkait yang valid
        $relatedProfile = null; // Inisialisasi di luar kondisi untuk jangkauan
        
        if ($user->roles === 'guru') {
            $relatedProfile = Guru::where('user_id', $user->id)->first();
            if ($relatedProfile && $jawaban->guru_id === $relatedProfile->id) {
                $allowed = true; // Guru bisa download jawaban untuk tugasnya
            }
        } elseif ($user->roles === 'orangtua') {
            $relatedProfile = Siswa::where('user_id', $user->id)->first(); // Asumsi user orangtua memiliki relasi langsung ke 1 siswa
            if ($relatedProfile && $jawaban->siswa_id === $relatedProfile->id) {
                $allowed = true; // Orang tua bisa download jawaban anak asuhnya
            }
            // Jika orangtua bisa memiliki banyak anak:
            // $siswaAsuhIds = $user->siswaAnak()->pluck('id')->toArray(); // Asumsi relasi 'siswaAnak' di model User
            // if (in_array($jawaban->siswa_id, $siswaAsuhIds)) {
            //     $allowed = true;
            // }

        } elseif ($user->roles === 'siswa') {
            $relatedProfile = Siswa::where('user_id', $user->id)->first();
            if ($relatedProfile && $jawaban->siswa_id === $relatedProfile->id) {
                $allowed = true; // Siswa bisa download jawaban miliknya sendiri
            }
        }

        if (!$allowed) {
            // Log peringatan lebih detail untuk debugging
            Log::warning('Download Jawaban Ditolak: User ID ' . ($user->id ?? 'N/A') .
                        ' dengan peran ' . ($user->roles ?? 'N/A') .
                        ' mencoba mengunduh jawaban ID ' . $jawaban->id .
                        '. Tidak memiliki izin. ' .
                        'Jawaban Guru ID: ' . ($jawaban->guru_id ?? 'N/A') .
                        ', Jawaban Siswa ID: ' . ($jawaban->siswa_id ?? 'N/A') .
                        ' | Profil Terkait (ID): ' . ($relatedProfile ? $relatedProfile->id : 'NULL'));
            abort(403, 'Anda tidak memiliki izin untuk mengunduh file ini.');
        }

        // Lanjutkan proses download jika diizinkan
        if (!$jawaban->file || !Storage::disk('public')->exists($jawaban->file)) {
            Log::warning('Download Jawaban Gagal: File jawaban ID ' . $jawaban->id . ' tidak ditemukan di storage: ' . ($jawaban->file ?? 'N/A'));
            abort(404, 'File jawaban tidak ditemukan atau telah dihapus.');
        }

        $path = Storage::disk('public')->path($jawaban->file);
        $fileName = basename($jawaban->file);
        return Response::download($path, $fileName);
    }

    /**
     * Menampilkan daftar tugas yang relevan untuk peran Orang Tua.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function orangtua()
    {
        // Lapisan keamanan kedua: pastikan pengguna yang login adalah orangtua
        if (Auth::user()->roles !== 'orangtua') {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $userOrangtua = Auth::user();

        // Mengambil profil siswa yang terkait dengan user orangtua.
        // Jika orangtua memiliki banyak anak, Anda perlu menyesuaikan ini.
        // Asumsi: user orangtua memiliki kolom di tabel siswa (misal: 'orangtua_user_id' di tabel siswa)
        // Atau relasi hasOne/hasMany di model User.
        // Kita akan coba mencari siswa berdasarkan user_id dari user orangtua,
        // jika struktur Anda adalah user orangtua berelasi langsung dengan 1 siswa.
        // Periksa model User dan Orangtua Anda untuk relasi yang benar
        $profilOrangtua = $userOrangtua->orangtua; // Ambil profil Orangtua dari user yang login

        $siswaAsuh = collect(); // Inisialisasi koleksi siswa asuh
        $errorMessage = null;

        if (is_null($profilOrangtua)) {
            $errorMessage = 'Profil orang tua tidak ditemukan untuk akun Anda. Pastikan data profil orang tua sudah diatur dengan benar.';
            Log::warning('DEBUG ORANGTUA: ' . $errorMessage . ' User ID: ' . ($userOrangtua->id ?? 'N/A'));
        } else {
            // Menggunakan relasi hasMany dari model Orangtua ke Siswa
            // Pastikan di App\Models\Orangtua.php ada: public function siswas() { return $this->hasMany(Siswa::class, 'orangtua_id'); }
            // Dimana 'orangtua_id' adalah foreign key di tabel 'siswas' yang merujuk ke 'orangtuas.id'
            $siswaAsuh = $profilOrangtua->siswas()->with('kelas')->get(); // Ambil semua anak dari profil orangtua, eager load kelas mereka

            if ($siswaAsuh->isEmpty()) {
                $errorMessage = 'Tidak ada anak yang terdaftar di bawah akun orang tua Anda.';
                Log::warning('DEBUG ORANGTUA: ' . $errorMessage . ' Orangtua ID: ' . $profilOrangtua->id);
            }
        }

        $tugas = collect();
        $jawabanSiswa = collect();

        // Proses tugas dan jawaban hanya jika ada siswa yang diasuh
        if ($siswaAsuh->isNotEmpty()) {
            foreach ($siswaAsuh as $anak) {
                if ($anak->kelas) {
                    // Ambil tugas untuk kelas anak ini
                    $tugasPerAnak = Tugas::with(['guru.mapel', 'kelas'])
                                         ->where('kelas_id', $anak->kelas->id)
                                         ->orderBy('created_at', 'desc')
                                         ->get();

                    // Attach the child object to each task for easy access in the view
                    foreach ($tugasPerAnak as $tugasItem) {
                        $tugasItem->related_siswa = $anak; // Tambahkan properti dynamic untuk menautkan siswa ke tugas
                        $tugas->push($tugasItem);
                    }
                    
                    // Ambil jawaban anak ini untuk tugas-tugasnya
                    $jawabanPerAnak = Jawaban::where('siswa_id', $anak->id)
                                             ->get()
                                             ->keyBy('tugas_id');
                    $jawabanSiswa = $jawabanSiswa->merge($jawabanPerAnak); // Gabungkan jawaban dari semua anak
                } else {
                    Log::warning('DEBUG ORANGTUA: Siswa ID ' . $anak->id . ' (' . ($anak->nama ?? 'N/A') . ') tidak memiliki kelas yang ditugaskan. Tugas tidak akan ditampilkan untuk siswa ini.');
                    // Tambahkan pesan error spesifik jika Anda ingin menampilkannya di view
                    // $errorMessage .= "Siswa " . ($anak->nama ?? 'Tanpa Nama') . " belum memiliki kelas.<br>";
                }
            }
            // Pastikan tugas unik jika ada tugas yang sama di beberapa kelas anak
            $tugas = $tugas->unique('id');
        }

        Log::info('DEBUG ORANGTUA: Memulai metode orangtua() untuk User ID: ' . ($userOrangtua->id ?? 'N/A'));
        Log::debug('DEBUG ORANGTUA: Profil Orangtua ditemukan (objek): ' . ($profilOrangtua ? json_encode($profilOrangtua->toArray()) : 'NULL'));
        Log::debug('DEBUG ORANGTUA: Jumlah Siswa Asuh yang ditemukan: ' . $siswaAsuh->count());
        Log::debug('DEBUG ORANGTUA: Jumlah Tugas yang ditemukan (total): ' . $tugas->count());
        Log::debug('DEBUG ORANGTUA: Jumlah Jawaban Siswa yang ditemukan (total): ' . $jawabanSiswa->count());


        $view = view('pages.orangtua.tugas', compact('tugas', 'siswaAsuh', 'jawabanSiswa'));

        if ($errorMessage) {
            $view->with('error', $errorMessage);
        }

        return $view;
    }
}
