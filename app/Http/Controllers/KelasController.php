<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan; // Pastikan ini sudah benar (App\Models\Jurusan)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt; // Diperlukan jika Anda menggunakan Crypt::encrypt/decrypt

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $guru = Guru::orderBy('nama', 'asc')->get();
        // Baris ini sudah benar, karena menggunakan Eloquent Model yang sudah menunjuk ke 'jurusans'
        $jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        return view('pages.admin.kelas.index', compact('kelas', 'guru', 'jurusan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404); // Fungsi ini tidak digunakan karena modal
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'guru_id' => 'required|unique:kelas,guru_id',
            // Perbaikan di sini: 'jurusan' diubah menjadi 'jurusans' (sudah benar, konfirmasi)
            'jurusan_id' => 'required|exists:jurusans,id',
        ], [
            'nama_kelas.required' => 'Nama Kelas wajib diisi.', // Menambahkan pesan required
            'nama_kelas.unique' => 'Nama Kelas sudah ada.',
            'guru_id.required' => 'Wali Kelas wajib dipilih.', // Menambahkan pesan required
            'guru_id.unique' => 'Guru sudah memiliki kelas.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan tidak ditemukan.',
        ]);

        Kelas::create($request->all());

        // --- PERBAIKAN PENTING DI SINI ---
        // Mengubah 'kelas.index' menjadi 'admin.kelas.index'
        return redirect()->route('admin.kelas.index')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        abort(404); // Fungsi ini tidak digunakan
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $guru = Guru::all();
        // Baris ini sudah benar
        $jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        return view('pages.admin.kelas.edit', compact('kelas', 'guru', 'jurusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            // Validasi nama_kelas: unique kecuali untuk ID kelas yang sedang diupdate
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $id,
            // Validasi guru_id: unique kecuali untuk ID kelas yang sedang diupdate
            'guru_id' => 'required|unique:kelas,guru_id,' . $id,
            // Perbaikan di sini: 'jurusan' diubah menjadi 'jurusans' (sudah benar, konfirmasi)
            'jurusan_id' => 'required|exists:jurusans,id',
        ], [
            'nama_kelas.required' => 'Nama Kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama Kelas sudah ada.',
            'guru_id.required' => 'Wali Kelas wajib dipilih.',
            'guru_id.unique' => 'Guru sudah memiliki kelas.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan tidak ditemukan.',
        ]);

        $data = $request->all();
        $kelas = Kelas::findOrFail($id);
        $kelas->update($data);

        // --- PERBAIKAN PENTING DI SINI ---
        // Mengubah 'kelas.index' menjadi 'admin.kelas.index'
        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Kelas::find($id)->delete();
        // Return back() tidak memerlukan perubahan nama rute karena kembali ke halaman sebelumnya
        return back()->with('success', 'Data kelas berhasil dihapus!');
    }
}
