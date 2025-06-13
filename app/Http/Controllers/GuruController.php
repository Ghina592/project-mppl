<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Mapel;
use App\Models\User;
use App\Models\Presensi; // Pastikan model Presensi diimpor jika digunakan di destroy
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage; // Pastikan ini diimpor untuk operasi file

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Pastikan Anda memuat relasi 'mapel' dan 'mapel.jurusan'
        // dan juga relasi 'user' untuk menampilkan data dari tabel users
        $guru = Guru::with(['mapel.jurusan', 'user'])->get();

        // Jika Anda perlu data mapel untuk form tambah guru di modal
        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get();

        return view('pages.admin.guru.index', compact('guru', 'mapel'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Biasanya mengembalikan view untuk form pembuatan.
        // Jika Anda menggunakan modal, Anda bisa mengosongkan ini atau abort 404.
        abort(404); // Sesuai dengan implementasi Anda yang menggunakan modal untuk tambah data
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required|string|max:255',
            'nip' => [
                'required',
                'numeric',
                'unique:gurus,nip', // Validasi unik di tabel gurus
                'unique:users,nip', // Validasi unik di tabel users
            ],
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
            'mapel_id' => 'required|exists:mapels,id', // Pastikan mapel_id ada di tabel mapels
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'nip.unique' => 'NIP sudah terdaftar.', // Pesan error kustom untuk NIP
            'mapel_id.required' => 'Mata Pelajaran wajib dipilih.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.numeric' => 'NIP harus berupa angka.',
            // Tambahkan pesan error kustom lainnya sesuai kebutuhan
        ]);

        DB::beginTransaction(); // Mulai transaksi database untuk atomicity

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('public/guru_photos');
            }

            // Buat user terlebih dahulu
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->nip . '@guru.com', // Pastikan email unik
                'password' => bcrypt('password'), // Atur password default (misal 'password')
                'roles' => 'guru', // Set role untuk user ini
                'nip' => $request->nip, // Simpan NIP di tabel users juga
            ]);

            // Buat guru dan kaitkan dengan user yang baru dibuat
            Guru::create([
                'user_id' => $user->id, // Kaitkan guru dengan user
                'nip' => $request->nip, // Simpan NIP di tabel gurus
                'nama' => $request->nama,
                'mapel_id' => $request->mapel_id,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'foto' => $fotoPath ? str_replace('public/', '', $fotoPath) : null,
            ]);

            DB::commit(); // Commit transaksi jika semua berhasil
            return back()->with('success', 'Data guru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada error
            // \Log::error("Error adding guru: " . $e->getMessage()); // Anda bisa mengaktifkan log ini
            return back()->with('error', 'Terjadi kesalahan saat menambahkan data guru: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Decrypt ID jika menggunakan Crypt::encrypt di route
        $decryptedId = Crypt::decrypt($id);
        // Eager load user juga untuk profil
        $guru = Guru::with(['mapel.jurusan', 'user'])->findOrFail($decryptedId);
        return view('pages.admin.guru.profile', compact('guru'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $guru = Guru::findOrFail($decryptedId);
        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get(); // Ambil semua mapel untuk dropdown

        return view('pages.admin.guru.edit', compact('guru', 'mapel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $this->validate($request, [
            'nama' => 'required|string|max:255',
            'nip' => [
                'required',
                'numeric',
                // NIP harus unik di tabel 'gurus', abaikan NIP saat ini
                Rule::unique('gurus', 'nip')->ignore($guru->id),
                // NIP juga harus unik di tabel 'users', abaikan NIP user terkait
                Rule::unique('users', 'nip')->ignore($guru->user->id ?? null), // Pastikan user ada
            ],
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
            'mapel_id' => 'required|exists:mapels,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'nip.unique' => 'NIP sudah terdaftar.', // Pesan error kustom untuk NIP
            'mapel_id.required' => 'Mata Pelajaran wajib dipilih.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.numeric' => 'NIP harus berupa angka.',
        ]);

        DB::beginTransaction(); // Mulai transaksi

        try {
            $fotoPath = $guru->foto; // Ambil foto lama
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($guru->foto && Storage::exists('public/' . $guru->foto)) {
                    Storage::delete('public/' . $guru->foto);
                }
                $fotoPath = $request->file('foto')->store('public/guru_photos');
                $fotoPath = str_replace('public/', '', $fotoPath); // Simpan path relatif
            }

            // Update data guru
            $guru->update([
                'nip' => $request->nip,
                'nama' => $request->nama,
                'mapel_id' => $request->mapel_id,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'foto' => $fotoPath,
            ]);

            // Update user terkait juga
            if ($guru->user) {
                $guru->user->update([
                    'name' => $request->nama,
                    'email' => $request->nip . '@guru.com',
                    'nip' => $request->nip, // Sinkronkan NIP di tabel users
                ]);
            }

            DB::commit(); // Commit transaksi
            return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbaharui!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi
            // \Log::error("Error updating guru: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data guru: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Cari guru berdasarkan ID, jika tidak ditemukan akan otomatis 404
        $guru = Guru::findOrFail($id);

        DB::beginTransaction(); // Mulai transaksi database

        try {
            // 1. Cek dan hapus foto dari storage
            if ($guru->foto) { // Cukup cek apakah ada nama foto
                // Path lengkap ke file: 'public/' + path foto yang disimpan
                $fotoPathOnDisk = 'public/' . $guru->foto;
                if (Storage::exists($fotoPathOnDisk)) {
                    Storage::delete($fotoPathOnDisk);
                }
            }

            // 2. Penting: Cek relasi yang mungkin mencegah penghapusan
            //    Jika guru memiliki catatan presensi, secara default tidak bisa dihapus
            //    kecuali foreign key di tabel 'presensis' diatur onDelete('cascade')
            //    atau onDelete('set null').
            //    Jika Anda ingin mencegah penghapusan guru yang masih punya presensi:
            if ($guru->presensis()->count() > 0) {
                DB::rollBack(); // Rollback transaksi karena tidak bisa dihapus
                return response()->json([
                    'message' => 'Guru ini tidak dapat dihapus karena masih memiliki catatan presensi yang terhubung. Mohon hapus presensi terkait terlebih dahulu.'
                ], 409); // 409 Conflict
            }

            //    Jika Anda ingin presensi ikut terhapus (atau diset NULL) saat guru dihapus,
            //    Anda harus mengatur foreign key di migrasi presensis table.
            //    Jika migrasi sudah diatur CASCADE, maka tidak perlu hapus manual di sini.
            //    Namun, jika tidak CASCADE dan ingin menghapus presensi, Anda bisa:
            //    $guru->presensis()->delete(); // Ini akan menghapus semua presensi terkait guru ini

            // 3. Hapus data User terkait terlebih dahulu (jika ada).
            //    Ini adalah praktik yang lebih baik jika User adalah "parent" dari Guru.
            //    Jika di migrasi tabel 'gurus' Anda sudah ada:
            //    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            //    Maka, penghapusan User akan OTOMATIS menghapus Guru yang terkait.
            //    Jadi, setelah user terhapus, guru juga akan terhapus secara otomatis.
            if ($guru->user) {
                // Pastikan user dihapus secara permanen karena model User kemungkinan juga tidak pakai soft deletes
                $guru->user->delete();
            }

            // 4. Hapus data Guru itu sendiri.
            //    Ini hanya akan dieksekusi jika User TIDAK ada, atau jika onDelete('cascade')
            //    tidak diatur pada user_id di tabel gurus, sehingga penghapusan user tidak menghapus guru.
            //    Namun, aman untuk tetap memanggilnya untuk memastikan penghapusan guru.
            //    Karena model Guru tidak menggunakan SoftDeletes, maka ini akan menghapus permanen.
            $guru->delete();


            DB::commit(); // Commit transaksi jika semua berhasil
            return response()->json(['message' => 'Data guru berhasil dihapus!'], 200); // Respon JSON untuk AJAX

        } catch (QueryException $e) {
            DB::rollBack(); // Rollback transaksi jika ada error database
            // Tangani error foreign key constraint (kode SQLSTATE 23000)
            if ($e->getCode() == '23000') {
                return response()->json([
                    'message' => 'Gagal menghapus guru. Guru ini masih memiliki data terkait (misalnya tugas, materi, jadwal, atau siswa di kelas) yang perlu dihapus atau diubah terlebih dahulu.'
                ], 409); // 409 Conflict
            }
            // Tangani error QueryException lainnya
            \Log::error("QueryException saat menghapus guru: " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan database saat menghapus data guru: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada error lain
            \Log::error("Error umum saat menghapus guru: " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan umum saat menghapus data guru: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }
}
