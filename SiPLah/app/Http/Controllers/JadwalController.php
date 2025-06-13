<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Menampilkan daftar semua resource jadwal.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Eager load relasi 'mapel' dan 'kelas' untuk menghindari N+1 query problem
        $jadwal = Jadwal::with(['mapel', 'kelas'])
                        ->orderBy('hari', 'asc')
                        ->orderBy('dari_jam', 'asc')
                        ->get();

        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get();
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        // Array hari yang lengkap dan konsisten dengan inputan (untuk dropdown di modal/create)
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        return view('pages.admin.jadwal.index', compact('jadwal', 'mapel', 'kelas', 'hari'));
    }

    /**
     * Menampilkan form untuk membuat resource jadwal baru.
     * Mengembalikan 404 karena form create diimplementasikan di modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Menyimpan resource jadwal yang baru dibuat ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'hari' => ['required', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])],
            'dari_jam' => 'required|date_format:H:i',
            'sampai_jam' => 'required|date_format:H:i|after:dari_jam',
        ], [
            'kelas_id.required' => 'Kelas wajib diisi.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'mapel_id.required' => 'Mata Pelajaran wajib diisi.',
            'mapel_id.exists' => 'Mata Pelajaran tidak valid.',
            'hari.required' => 'Hari wajib diisi.',
            'hari.in' => 'Hari tidak valid.',
            'dari_jam.required' => 'Jam mulai wajib diisi.',
            'dari_jam.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'sampai_jam.required' => 'Jam selesai wajib diisi.',
            'sampai_jam.date_format' => 'Format jam selesai tidak valid (HH:MM).',
            'sampai_jam.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Cek apakah ada jadwal lain untuk kelas, hari, dan rentang jam yang sama.
        $existingJadwal = Jadwal::where('kelas_id', $validatedData['kelas_id'])
            ->where('hari', strtolower($validatedData['hari']))
            ->where(function ($query) use ($validatedData) {
                // Pengecekan tabrakan jadwal
                $query->where(function($q) use ($validatedData) {
                        $q->where('dari_jam', '<', $validatedData['sampai_jam'])
                          ->where('sampai_jam', '>', $validatedData['dari_jam']);
                    });
            })
            ->first();

        if ($existingJadwal) {
            return back()->withErrors([
                'jam_tabrakan' => 'Jadwal untuk kelas ini pada hari dan jam tersebut sudah ada atau bertabrakan.'
            ])->withInput();
        }

        Jadwal::create([
            'kelas_id' => $validatedData['kelas_id'],
            'mapel_id' => $validatedData['mapel_id'],
            'hari' => strtolower($validatedData['hari']),
            'dari_jam' => $validatedData['dari_jam'],
            'sampai_jam' => $validatedData['sampai_jam'],
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil dibuat!');
    }

    /**
     * Menampilkan resource jadwal yang ditentukan.
     * Mengembalikan 404 karena tidak ada halaman detail untuk jadwal.
     *
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\Http\Response
     */
    public function show(Jadwal $jadwal) // Menggunakan Route Model Binding
    {
        abort(404);
    }

    /**
     * Menampilkan form untuk mengedit resource jadwal yang ditentukan.
     *
     * @param  \App\Models\Jadwal  $jadwal // Menggunakan Route Model Binding
     * @return \Illuminate\View\View
     */
    public function edit(Jadwal $jadwal)
    {
        // $jadwal sudah berisi objek Jadwal yang ditemukan berdasarkan ID di URL

        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get();
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        // Menggunakan array hari yang lengkap dan konsisten dengan inputan
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        return view('pages.admin.jadwal.edit', compact('jadwal', 'mapel', 'kelas', 'hari'));
    }

    /**
     * Memperbarui resource jadwal yang ditentukan di penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jadwal  $jadwal // Menggunakan Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        // $jadwal sudah berisi objek Jadwal yang ditemukan berdasarkan ID di URL

        $validatedData = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'hari' => ['required', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])],
            'dari_jam' => 'required|date_format:H:i',
            'sampai_jam' => 'required|date_format:H:i|after:dari_jam',
        ], [
            'kelas_id.required' => 'Kelas wajib diisi.',
            'mapel_id.required' => 'Mata Pelajaran wajib diisi.',
            'hari.required' => 'Hari wajib diisi.',
            'dari_jam.required' => 'Jam mulai wajib diisi.',
            'sampai_jam.required' => 'Jam selesai wajib diisi.',
            'sampai_jam.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Perbaikan validasi unik untuk update (mengabaikan jadwal yang sedang diedit)
        $existingJadwal = Jadwal::where('kelas_id', $validatedData['kelas_id'])
            ->where('hari', strtolower($validatedData['hari']))
            ->where(function ($query) use ($validatedData) {
                // Pengecekan tabrakan jadwal
                $query->where(function($q) use ($validatedData) {
                        $q->where('dari_jam', '<', $validatedData['sampai_jam'])
                          ->where('sampai_jam', '>', $validatedData['dari_jam']);
                    });
            })
            ->where('id', '!=', $jadwal->id) // Abaikan jadwal yang sedang diedit
            ->first();

        if ($existingJadwal) {
            return back()->withErrors([
                'jam_tabrakan' => 'Jadwal untuk kelas ini pada hari dan jam tersebut sudah ada atau bertabrakan.'
            ])->withInput();
        }

        $jadwal->update([
            'kelas_id' => $validatedData['kelas_id'],
            'mapel_id' => $validatedData['mapel_id'],
            'hari' => strtolower($validatedData['hari']),
            'dari_jam' => $validatedData['dari_jam'],
            'sampai_jam' => $validatedData['sampai_jam'],
        ]);

        // BARIS INI YANG HARUS DIUBAH (line 188 dalam kode Anda yang asli)
        // Dari 'jadwal.index' menjadi 'admin.jadwal.index'
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbaharui!');
    }

    /**
     * Menghapus resource jadwal yang ditentukan dari penyimpanan.
     *
     * @param  \App\Models\Jadwal  $jadwal // Menggunakan Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Jadwal $jadwal) // Ini adalah baris 197 yang akan dieksekusi jika Anda menggunakan kode ini.
    {
        // $jadwal sudah berisi objek Jadwal yang ditemukan berdasarkan ID di URL
        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }
}