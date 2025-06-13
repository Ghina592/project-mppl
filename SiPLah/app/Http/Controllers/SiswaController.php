<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\User;     // Pastikan model User di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log; // Untuk logging error
use Illuminate\Support\Facades\Storage; // Untuk menghapus file foto
use Illuminate\Support\Facades\Hash;   // Tambahkan ini untuk mengenkripsi password
use Illuminate\Support\Str;             // Tambahkan ini untuk membuat string acak/token
use Illuminate\Support\Facades\DB;      // Tambahkan ini untuk transaksi database

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Mengambil semua data siswa dengan eager loading untuk relasi 'kelas' dan 'presensis'
            $siswa = Siswa::with(['kelas', 'presensis'])->get();

            // Mengambil semua data kelas untuk dropdown di modal
            $kelas = Kelas::all();

            // Debugging: Uncomment baris ini untuk melihat data siswa yang diambil
            // dd($siswa);

            // Perubahan path view: 'admin.siswa.index' menjadi 'pages.admin.siswa.index'
            return view('pages.admin.siswa.index', compact('siswa', 'kelas'));

        } catch (\Exception $e) {
            Log::error('Error fetching student data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load student data. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     * (Biasanya tidak digunakan jika form ada di dalam modal)
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:siswas,nis|max:20', // Tambah max:20 agar konsisten dengan telp
            'telp' => 'nullable|string|max:20',
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        try {
            // Memulai transaksi database untuk memastikan data siswa dan user tersimpan bersamaan
            DB::beginTransaction();

            // Tangani unggahan foto
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('public/fotos_siswa');
                $validatedData['foto'] = Storage::url($path); // Simpan URL publik
            } else {
                $validatedData['foto'] = null; // Pastikan null jika tidak ada foto
            }

            // 1. Buat akun User terlebih dahulu
            // Gunakan NIS sebagai email dengan domain dummy, atau generate unik
            // Anda bisa menambahkan field email di form tambah siswa jika diperlukan
            $email = $validatedData['nis'] . '@siswa.com'; // Contoh email otomatis menggunakan NIS
            $password = $validatedData['nis'] . 123; // Contoh password acak 8 karakter
            // Atau bisa juga menggunakan password default yang mudah diingat dan wajibkan ganti saat login pertama
            // $password = 'password123';

            $user = User::create([
                'name' => $validatedData['nama'],
                'email' => $email,
                'password' => Hash::make($password), // Enkripsi password
                'roles' => 'siswa', // Set peran sebagai siswa
            ]);

            // Tambahkan log untuk password yang dibuat (hati-hati di lingkungan produksi, jangan log password plain)
            Log::info("User created for student: {$validatedData['nama']} with email: {$email} and password: {$password}");

            // 2. Buat data Siswa dan hubungkan dengan User yang baru dibuat
            $siswa = Siswa::create(array_merge($validatedData, [
                'user_id' => $user->id, // Hubungkan siswa dengan user
            ]));

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('admin.siswa.index')
                             ->with('success', 'Data siswa dan akun user berhasil ditambahkan! Email: ' . $email . ', Password: ' . $password);

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            Log::error('Error adding student and user account: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data siswa dan akun user. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Menggunakan $id langsung jika tidak di-enkripsi di route,
            // atau jika Anda sudah menggunakan Route Model Binding (direkomendasikan)
            // Namun, karena blade sebelumnya menggunakan Crypt::encrypt, kita tetap gunakan Crypt::decrypt
            $siswa = Siswa::with(['kelas', 'presensis', 'orangtua', 'user'])->findOrFail(Crypt::decrypt($id));
            return view('pages.admin.siswa.profile', compact('siswa'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('SiswaController@show: DecryptException for ID: ' . $id . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'ID siswa tidak valid.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('SiswaController@show: Siswa not found for ID: ' . (isset($decryptedId) ? $decryptedId : $id) . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'Siswa tidak ditemukan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $siswa = Siswa::with(['kelas'])->findOrFail(Crypt::decrypt($id));
            $kelas = Kelas::all();
            return view('pages.admin.siswa.edit', compact('siswa', 'kelas'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('SiswaController@edit: DecryptException for ID: ' . $id . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'ID siswa tidak valid.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('SiswaController@edit: Siswa not found for ID: ' . (isset($decryptedId) ? $decryptedId : $id) . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'Siswa tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Load relasi user saat mengambil siswa
            $siswa = Siswa::with('user')->findOrFail(Crypt::decrypt($id));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('SiswaController@update: DecryptException for ID: ' . $id . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'ID siswa tidak valid.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('SiswaController@update: Siswa not found for ID: ' . (isset($decryptedId) ? $decryptedId : $id) . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'Siswa tidak ditemukan.');
        }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:siswas,nis,' . $siswa->id,
            'telp' => 'nullable|string|max:20',
            'kelas_id' => 'required|exists:kelas,id',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Update data Siswa
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($siswa->foto && Storage::exists(str_replace('storage/', 'public/', $siswa->foto))) {
                    Storage::delete(str_replace('storage/', 'public/', $siswa->foto));
                }
                $path = $request->file('foto')->store('public/fotos_siswa');
                $validatedData['foto'] = Storage::url($path);
            }

            $siswa->update($validatedData);

            // Update akun User terkait (jika ada)
            if ($siswa->user) {
                $siswa->user->update([
                    'name' => $validatedData['nama'],
                    // Anda mungkin ingin menambahkan logika untuk update email di sini,
                    // tetapi berhati-hatilah jika email didasarkan pada NIS, karena NIS bisa berubah.
                    // Jika NIS berubah, email user juga harus diupdate atau logic email harus diubah.
                ]);
                Log::info('User account updated for student: ' . $siswa->nama);
            } else {
                Log::warning('Siswa ID ' . $siswa->id . ' tidak memiliki akun user terkait saat update. Membuat akun user baru.');
                // Jika tidak ada user terkait, buat user baru (ini opsional, tergantung alur bisnis Anda)
                $email = $validatedData['nis'] . '@siswa.com';
                $password = $validatedData['nis'] . 123; // Atau password default
                $user = User::create([
                    'name' => $validatedData['nama'],
                    'email' => $email,
                    'password' => Hash::make($password),
                    'roles' => 'siswa',
                ]);
                $siswa->update(['user_id' => $user->id]); // Hubungkan user baru ke siswa
                Log::info("New user account created during student update for: {$validatedData['nama']} with email: {$email} and password: {$password}");
            }

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('admin.siswa.index')->with('success', 'Data siswa dan akun user berhasil diperbarui!');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            Log::error('Error updating student and user account: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data siswa dan akun user. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Karena Anda menggunakan $id langsung, pastikan rute mengirimkan ID yang TIDAK terenkripsi
            // Jika rute Anda dienkripsi, gunakan: $siswa = Siswa::with('user')->findOrFail(Crypt::decrypt($id));
            $siswa = Siswa::with('user')->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('SiswaController@destroy: Siswa not found for ID: ' . $id . ' - ' . $e->getMessage());
            return redirect()->route('admin.siswa.index')->with('error', 'Siswa tidak ditemukan.');
        }

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Hapus foto jika ada
            if ($siswa->foto && Storage::exists(str_replace('storage/', 'public/', $siswa->foto))) {
                Storage::delete(str_replace('storage/', 'public/', $siswa->foto));
                Log::info('Foto siswa dihapus: ' . $siswa->foto);
            }

            // Hapus akun user terkait jika ada
            if ($siswa->user) {
                $siswa->user->delete();
                Log::info('Akun user terkait siswa dihapus: ' . $siswa->user->email);
            } else {
                Log::warning('Siswa ID ' . $siswa->id . ' tidak memiliki akun user terkait untuk dihapus.');
            }

            // Hapus record siswa
            $siswa->delete();

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            return redirect()->route('admin.siswa.index')->with('success', 'Data siswa dan akun user berhasil dihapus!');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            Log::error('Error deleting student and user account: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Gagal menghapus data siswa dan akun user. ' . $e->getMessage());
        }
    }
}
