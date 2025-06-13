<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Mapel;
use Illuminate\Http\Request;
// Hapus atau komentari baris di bawah ini jika Anda tidak menggunakan Crypt::encrypt/decrypt
// use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    /**
     * Menampilkan daftar semua resource mata pelajaran.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        $mapel = Mapel::orderBy('nama_mapel', 'asc')->get();

        return view('pages.admin.mapel.index', compact('mapel', 'jurusan'));
    }

    /**
     * Menampilkan form untuk membuat resource mata pelajaran baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Menyimpan resource mata pelajaran yang baru dibuat ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel',
            'jurusan_id' => 'required|exists:jurusans,id',
        ], [
            'nama_mapel.required' => 'Nama Mata Pelajaran wajib diisi.',
            'nama_mapel.unique' => 'Nama Mata Pelajaran sudah ada.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        // Menggunakan Mapel::create() jika ini memang hanya untuk membuat baru
        // Jika ada logika upsert (create jika tidak ada, update jika ada), Mapel::updateOrCreate() bisa tetap digunakan
        Mapel::create($validatedData);

        return redirect()->route('admin.mapel.index')->with('success', 'Data mapel berhasil dibuat!');
    }

    /**
     * Menampilkan resource mata pelajaran yang ditentukan.
     *
     * @param  \App\Models\Mapel  $mapel // Menggunakan Route Model Binding
     * @return \Illuminate\Http\Response
     */
    public function show(Mapel $mapel)
    {
        abort(404);
    }

    /**
     * Menampilkan form untuk mengedit resource mata pelajaran yang ditentukan.
     *
     * @param  \App\Models\Mapel  $mapel // Menggunakan Route Model Binding
     * @return \Illuminate\View\View
     */
    public function edit(Mapel $mapel)
    {
        // $mapel sudah berisi objek Mapel yang ditemukan berdasarkan ID di URL
        // Tidak perlu lagi Crypt::decrypt($id) atau findOrFail($id)

        $jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();

        return view('pages.admin.mapel.edit', compact('mapel', 'jurusan'));
    }

    /**
     * Memperbarui resource mata pelajaran yang ditentukan di penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mapel  $mapel // Menggunakan Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Mapel $mapel)
    {
        // $mapel sudah berisi objek Mapel yang ditemukan berdasarkan ID di URL
        // Tidak perlu lagi Crypt::decrypt($id) atau findOrFail($id)

        $validatedData = $request->validate([
            'nama_mapel' => [
                'required',
                // Mengabaikan mapel yang sedang diedit dari validasi unique
                Rule::unique('mapels', 'nama_mapel')->ignore($mapel->id), // Gunakan $mapel->id
            ],
            'jurusan_id' => 'required|exists:jurusans,id',
        ], [
            'nama_mapel.required' => 'Nama Mata Pelajaran wajib diisi.',
            'nama_mapel.unique' => 'Nama Mata Pelajaran sudah ada.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
        ]);

        $mapel->update($validatedData);

        return redirect()->route('admin.mapel.index')->with('success', 'Data mapel berhasil diperbarui!');
    }

    /**
     * Menghapus resource mata pelajaran yang ditentukan dari penyimpanan.
     *
     * @param  \App\Models\Mapel  $mapel // Menggunakan Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Mapel $mapel) // Ini adalah baris 111 yang akan dieksekusi jika Anda menggunakan kode ini.
    {
        // $mapel sudah berisi objek Mapel yang ditemukan berdasarkan ID di URL
        // Tidak perlu lagi Crypt::decrypt($id) atau findOrFail($id)
        $mapel->delete();

        return back()->with('success', 'Data mata pelajaran berhasil dihapus!');
    }
}