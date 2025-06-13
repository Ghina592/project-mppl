<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Orangtua;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Support\Facades\Log; // Gunakan Facade Log untuk logging

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Eager load relasi yang mungkin dibutuhkan di view untuk performa
        // Gunakan pagination untuk daftar user yang panjang
        $user = User::with(['guru', 'siswa', 'orangtua'])->orderBy('roles', 'asc')->paginate(10);
        // Pastikan siswaList di-eager load user-nya untuk menghindari N+1 query di blade
        $siswaList = Siswa::with('user')->orderBy('nama')->get(); // Diperlukan untuk dropdown siswa di form orangtua

        // Juga kirim mapels dan kelas ke view karena modal ada di index
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();

        // View untuk index admin ada di pages.admin.user.index
        return view('pages.admin.user.index', compact('user', 'siswaList', 'mapels', 'kelas'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Mengarahkan ke 404 karena pembuatan user dilakukan melalui modal di index
        abort(404);
    }

    /**
     * Menyimpan data pengguna baru.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'in:admin,guru,siswa,orangtua'],
        ];

        $messages = [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'roles.required' => 'Roles wajib dipilih.',
            'roles.in' => 'Roles tidak valid.',
        ];

        // Tambahkan aturan validasi kondisional untuk field dinamis
        if ($request->roles === 'guru') {
            $rules['nip'] = ['required', 'string', 'unique:gurus,nip']; // Unique di tabel 'gurus'
            // nip_user tidak lagi relevan karena NIP hanya disimpan di tabel gurus
            $rules['mapel_id_guru'] = ['required', 'exists:mapels,id']; // Validasi mapel_id guru
            $rules['no_telp_guru'] = ['nullable', 'string', 'max:255'];
            $rules['alamat_guru'] = ['nullable', 'string', 'max:255'];

            $messages['nip.required'] = 'NIP Guru wajib diisi.';
            $messages['nip.unique'] = 'NIP Guru sudah terdaftar.';
            $messages['mapel_id_guru.required'] = 'Mata Pelajaran Guru wajib dipilih.';
            $messages['mapel_id_guru.exists'] = 'Mata Pelajaran Guru tidak valid.';

        } elseif ($request->roles === 'siswa') {
            $rules['nis'] = ['required', 'string', 'unique:siswas,nis']; // Unique di tabel 'siswas'
            // nis_user tidak lagi relevan karena NIS hanya disimpan di tabel siswas
            $rules['kelas_id_siswa'] = ['required', 'exists:kelas,id']; // Validasi kelas_id siswa
            $rules['telp_siswa'] = ['nullable', 'string', 'max:255'];
            $rules['alamat_siswa'] = ['nullable', 'string', 'max:255'];

            $messages['nis.required'] = 'NIS Siswa wajib diisi.';
            $messages['nis.unique'] = 'NIS Siswa sudah terdaftar.';
            $messages['kelas_id_siswa.required'] = 'Kelas siswa wajib dipilih.';
            $messages['kelas_id_siswa.exists'] = 'Kelas siswa tidak valid.';

        } elseif ($request->roles === 'orangtua') {
            $rules['no_telp'] = ['required', 'string', 'max:255'];
            $rules['alamat'] = ['required', 'string', 'max:255'];
            $rules['siswas_selected'] = ['nullable', 'array', 'exists:siswas,id'];
            $messages['no_telp.required'] = 'Nomor Telepon Orang Tua wajib diisi.';
            $messages['alamat.required'] = 'Alamat Orang Tua wajib diisi.';
            $messages['siswas_selected.exists'] = 'Salah satu Siswa yang dipilih tidak valid.';
        }

        $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // 1. Buat User Utama
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'roles' => $request->roles,
                // Kolom 'nis' dan 'nip' di tabel 'users' dihapus jika tidak ada di skema DB
                // atau jika data spesifiknya hanya di tabel 'gurus'/'siswas'
                // 'nis' => ($request->roles === 'siswa') ? $request->nis_user : null, // Dihapus
                // 'nip' => ($request->roles === 'guru') ? $request->nip_user : null, // Dihapus
            ]);

            // 2. Simpan Data Terkait Berdasarkan Roles
            if ($request->roles === 'guru') {
                Guru::create([
                    'user_id' => $user->id,
                    'nip' => $request->nip, // NIP dari form untuk tabel gurus
                    'nama' => $request->name, // Mengambil nama dari request
                    'mapel_id' => $request->input('mapel_id_guru'),
                    'no_telp' => $request->input('no_telp_guru', '-'), // Konsisten dengan Blade
                    'alamat' => $request->input('alamat_guru', '-'), // Konsisten dengan Blade
                ]);
            } elseif ($request->roles === 'siswa') {
                Siswa::create([
                    'user_id' => $user->id,
                    'nis' => $request->nis, // NIS dari form untuk tabel siswas
                    'nama' => $request->name, // Mengambil nama dari request
                    'kelas_id' => $request->input('kelas_id_siswa'),
                    'telp' => $request->input('telp_siswa', '-'), // Konsisten dengan Blade
                    'alamat' => $request->input('alamat_siswa', '-'), // Konsisten dengan Blade
                ]);
            } elseif ($request->roles === 'orangtua') {
                $orangtua = Orangtua::create([
                    'user_id' => $user->id,
                    'nama' => $request->name, // Pastikan ada kolom 'nama' di tabel 'orangtuas'
                    'alamat' => $request->alamat,
                    'no_telp' => $request->no_telp,
                ]);

                // Sinkronkan siswa yang terkait dengan orang tua ini
                if ($request->has('siswas_selected') && is_array($request->siswas_selected)) {
                    $orangtua->siswas()->sync($request->siswas_selected);
                } else {
                    $orangtua->siswas()->detach(); // Detach semua jika tidak ada yang dipilih
                }
            }

            DB::commit();
            // --- PERBAIKAN PENTING DI SINI ---
            // Mengubah 'user.index' menjadi 'admin.user.index'
            return redirect()->route('admin.user.index')->with('success', 'Data user berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat menambahkan user: " . $e->getMessage() . " - " . $e->getFile() . ":" . $e->getLine());

            // ************ PERHATIAN: AKTIFKAN BARIS INI SEMENTARA UNTUK DEBUGGING ************
            throw $e; // <--- Ini harus aktif untuk melihat error database
            // ******************************************************************************************

            // return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi. Detail error: ' . $e->getMessage());
        }
    }

    /**
     * Mencegah akses langsung ke halaman show (jika tidak ada).
     * @return void
     */
    public function show()
    {
        abort(404);
    }

    /**
     * Menampilkan form edit profil pengguna.
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Pastikan hanya admin yang bisa mengedit user melalui route resource
        if (auth()->check() && auth()->user()->roles !== 'admin') {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses.');
        }

        $guru = null;
        $siswa = null;
        $orangtua = null;
        $siswasAll = Siswa::with('user')->orderBy('nama')->get(); // Eager load user untuk nama siswa di dropdown
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();


        if ($user->roles === 'guru') {
            $guru = $user->guru; // Akses relasi langsung
        } elseif ($user->roles === 'siswa') {
            $siswa = $user->siswa; // Akses relasi langsung
        } elseif ($user->roles === 'orangtua') {
            $orangtua = $user->orangtua; // Akses relasi langsung
        }

        return view('pages.admin.user.edit', compact('user', 'guru', 'siswa', 'orangtua', 'siswasAll', 'mapels', 'kelas'));
    }

    /**
     * Memperbarui data profil pengguna.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Pastikan hanya admin yang bisa mengupdate user melalui route resource
        if (auth()->check() && auth()->user()->roles !== 'admin') {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id), // Unik kecuali user ini sendiri
            ],
            'roles' => ['required', 'in:admin,guru,siswa,orangtua'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'roles.required' => 'Roles wajib dipilih.',
            'roles.in' => 'Roles tidak valid.',
        ];

        // Tambahkan aturan validasi kondisional untuk field dinamis saat update
        if ($request->roles === 'guru') {
            $rules['nip'] = [
                'required',
                'string',
                // NIP harus unik, kecuali untuk guru ini sendiri (melalui tabel gurus)
                Rule::unique('gurus', 'nip')->ignore($user->guru->id ?? null),
            ];
            // nip_user tidak lagi relevan
            // $rules['nip_user'] = ['nullable', 'string', Rule::unique('users', 'nip')->ignore($user->id)]; // Dihapus

            $messages['nip.required'] = 'NIP Guru wajib diisi.';
            $messages['nip.unique'] = 'NIP Guru sudah terdaftar.';
            // $messages['nip_user.unique'] = 'NIP sudah terdaftar pada user lain.'; // Dihapus

            $rules['alamat_guru'] = ['nullable', 'string', 'max:255']; // Konsisten dengan Blade
            $rules['no_telp_guru'] = ['nullable', 'string', 'max:255']; // Konsisten dengan Blade
            $rules['mapel_id_guru'] = ['required', 'exists:mapels,id']; // Validasi mapel_id guru
            $messages['mapel_id_guru.required'] = 'Mata Pelajaran Guru wajib dipilih.';
            $messages['mapel_id_guru.exists'] = 'Mata Pelajaran Guru tidak valid.';

        } elseif ($request->roles === 'siswa') {
            $rules['nis'] = [
                'required',
                'string',
                // NIS harus unik, kecuali untuk siswa ini sendiri (melalui tabel siswas)
                Rule::unique('siswas', 'nis')->ignore($user->siswa->id ?? null),
            ];
            // nis_user tidak lagi relevan
            // $rules['nis_user'] = ['nullable', 'string', Rule::unique('users', 'nis')->ignore($user->id)]; // Dihapus

            $messages['nis.required'] = 'NIS Siswa wajib diisi.';
            $messages['nis.unique'] = 'NIS Siswa sudah terdaftar.';
            // $messages['nis_user.unique'] = 'NIS sudah terdaftar pada user lain.'; // Dihapus

            $rules['alamat_siswa'] = ['nullable', 'string', 'max:255']; // Konsisten dengan Blade
            $rules['telp_siswa'] = ['nullable', 'string', 'max:255']; // Konsisten dengan Blade
            $rules['kelas_id_siswa'] = ['required', 'exists:kelas,id'];
            $messages['kelas_id_siswa.required'] = 'Kelas siswa wajib dipilih.';
            $messages['kelas_id_siswa.exists'] = 'Kelas siswa tidak valid.';

        } elseif ($request->roles === 'orangtua') {
            $rules['no_telp'] = ['required', 'string', 'max:255'];
            $rules['alamat'] = ['required', 'string', 'max:255'];
            $rules['siswas_selected'] = ['nullable', 'array', 'exists:siswas,id'];
            $messages['no_telp.required'] = 'Nomor Telepon Orang Tua wajib diisi.';
            $messages['alamat.required'] = 'Alamat Orang Tua wajib diisi.';
            $messages['siswas_selected.exists'] = 'Salah satu Siswa yang dipilih tidak valid.';
        }

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            // Simpan peran asli sebelum diubah untuk logic penghapusan
            $originalRole = $user->getOriginal('roles');

            // Update data user utama
            $user->name = $request->name;
            $user->email = $request->email;
            $user->roles = $request->roles; // Role juga bisa diupdate
            // nis/nip di tabel users tidak lagi diupdate dari form dinamis
            // $user->nis = ($request->roles === 'siswa') ? $request->nis_user : null; // Dihapus
            // $user->nip = ($request->roles === 'guru') ? $request->nip_user : null; // Dihapus

            if ($request->filled('password')) { // Jika password diisi, update password
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Handle perubahan role: hapus record lama jika role berubah
            if ($originalRole !== $user->roles) { // Jika role berubah dari yang asli
                if ($originalRole === 'guru' && $user->guru) {
                    $user->guru->delete();
                } elseif ($originalRole === 'siswa' && $user->siswa) {
                    $user->siswa->delete();
                } elseif ($originalRole === 'orangtua' && $user->orangtua) {
                    $user->orangtua->siswas()->detach(); // Detach dulu relasi many-to-many
                    $user->orangtua->delete();
                }
            }

            // Update/Create record baru berdasarkan role yang baru
            if ($user->roles === 'guru') {
                Guru::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama' => $request->name, // Pastikan nama diisi untuk guru
                        'nip' => $request->nip,
                        'alamat' => $request->input('alamat_guru', '-'), // Konsisten dengan Blade
                        'no_telp' => $request->input('no_telp_guru', '-'), // Konsisten dengan Blade
                        'mapel_id' => $request->input('mapel_id_guru'), // Ambil dari input form
                    ]
                );
            } else if ($user->roles === 'siswa') {
                Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama' => $request->name, // Pastikan nama diisi untuk siswa
                        'nis' => $request->nis,
                        'alamat' => $request->input('alamat_siswa', '-'), // Konsisten dengan Blade
                        'telp' => $request->input('telp_siswa', '-'), // Konsisten dengan Blade
                        'kelas_id' => $request->kelas_id_siswa, // Ambil dari input form
                    ]
                );
            } else if ($user->roles === 'orangtua') {
                $orangtua = Orangtua::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama' => $request->name, // Pastikan nama diisi di sini
                        'no_telp' => $request->no_telp,
                        'alamat' => $request->alamat,
                    ]
                );

                if ($request->has('siswas_selected') && is_array($request->siswas_selected)) {
                    $orangtua->siswas()->sync($request->siswas_selected);
                } else {
                    $orangtua->siswas()->detach(); // Jika tidak ada siswa dipilih, detach semua
                }
            }

            DB::commit();
            // --- PERBAIKAN PENTING DI SINI ---
            // Mengubah 'user.index' menjadi 'admin.user.index'
            return redirect()->route('admin.user.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat mengupdate user: " . $e->getMessage() . " - " . $e->getFile() . ":" . $e->getLine());
            // ************ PERHATIAN: AKTIFKAN BARIS INI SEMENTARA UNTUK DEBUGGING ************
            throw $e; // <--- Ini harus aktif untuk melihat error database
            // ******************************************************************************************
            // return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengubah data. Silakan coba lagi. Detail error: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data pengguna.
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            // Hapus data terkait di tabel lain berdasarkan roles
            if ($user->roles === 'guru' && $user->guru) {
                $user->guru->delete();
            } elseif ($user->roles === 'siswa' && $user->siswa) {
                $user->siswa->delete();
            } elseif ($user->roles === 'orangtua' && $user->orangtua) {
                // Detach siswa sebelum menghapus orangtua (jika ada relasi many-to-many)
                $user->orangtua->siswas()->detach();
                $user->orangtua->delete();
            }

            // Hapus user itu sendiri
            $user->delete();

            DB::commit();
            // --- PERBAIKAN PENTING DI SINI ---
            // Mengubah 'user.index' menjadi 'admin.user.index'
            return back()->with('success', 'Data user berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat menghapus user: " . $e->getMessage() . " - " . $e->getFile() . ":" . $e->getLine());
            // ************ PERHATIAN: AKTIFKAN BARIS INI SEMENTARA UNTUK DEBUGGING ************
            throw $e; // <--- Ini harus aktif untuk melihat error database
            // ******************************************************************************************
            // return back()->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi. Detail error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form ubah password.
     * @return \Illuminate\View\View
     */
    public function editPassword()
    {
        $user = Auth::user();
        $guru = null;
        $siswa = null;
        $orangtua = null;

        if ($user->roles === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
        } elseif ($user->roles === 'siswa') {
            $siswa = Siswa::where('user_id', $user->id)->first();
        } elseif ($user->roles === 'orangtua') {
            $orangtua = Orangtua::where('user_id', $user->id)->first();
        }

        return view('pages.ubah-password', compact('user', 'guru', 'siswa', 'orangtua'));
    }

    /**
     * Memperbarui password pengguna.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Validasi password lama
        if (!Hash::check($request->get('current-password'), $user->password)) {
            return redirect()->back()->with("error", "Password lama tidak sesuai");
        }

        // Validasi password baru tidak boleh sama dengan password lama
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            return redirect()->back()->with("error", "Password baru tidak boleh sama dengan password lama");
        }

        // Validasi password baru dan konfirmasinya
        $request->validate([
            'current-password' => ['required'],
            'new-password' => ['required', 'string', 'min:8', 'confirmed'], // Min 8 karakter
        ], [
            'current-password.required' => 'Password lama wajib diisi.',
            'new-password.required' => 'Password baru wajib diisi.',
            'new-password.min' => 'Password baru minimal 8 karakter.',
            'new-password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Ubah Password
        $user->password = Hash::make($request->get('new-password'));
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diubah');
    }
}
