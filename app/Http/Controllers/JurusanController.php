<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JurusanController extends Controller
{
    /**
     * Menampilkan daftar semua resource jurusan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengurutkan berdasarkan nama_jurusan secara ascending
        $jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        return view('pages.admin.jurusan.index', compact('jurusan'));
    }

    /**
     * Menampilkan form untuk membuat resource jurusan baru.
     * Metode ini saat ini mengembalikan 404 karena Anda tidak ingin form create ditampilkan.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Jika Anda tidak ingin fitur create, tetap abort 404
        abort(404);
    }

    /**
     * Menyimpan resource jurusan yang baru dibuat ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_jurusan' => 'required|unique:jurusans,nama_jurusan',
        ], [
            'nama_jurusan.required' => 'Nama Jurusan wajib diisi.',
            'nama_jurusan.unique' => 'Nama Jurusan sudah ada.',
        ]);

        Jurusan::create($validatedData);

        // Jika Anda ingin mengarahkan kembali ke daftar jurusan setelah store
        // Maka ini juga harus admin.jurusan.index
        return redirect()->route('admin.jurusan.index')->with('success', 'Data jurusan berhasil dibuat!');
        // Atau jika ingin tetap ke halaman sebelumnya
        // return back()->with('success', 'Data jurusan berhasil dibuat!');
    }

    /**
     * Menampilkan resource jurusan yang ditentukan.
     * Metode ini saat ini mengembalikan 404 karena Anda tidak ingin form show ditampilkan.
     *
     * @param  \App\Models\Jurusan  $jurusan
     * @return \Illuminate\Http\Response
     */
    public function show(Jurusan $jurusan)
    {
        abort(404);
    }

    /**
     * Menampilkan form untuk mengedit resource jurusan yang ditentukan.
     *
     * @param  \App\Models\Jurusan  $jurusan
     * @return \Illuminate\View\View
     */
    public function edit(Jurusan $jurusan)
    {
        return view('pages.admin.jurusan.edit', compact('jurusan'));
    }

    /**
     * Memperbarui resource jurusan yang ditentukan di penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jurusan  $jurusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $validatedData = $request->validate([
            'nama_jurusan' => [
                'required',
                Rule::unique('jurusans', 'nama_jurusan')->ignore($jurusan->id),
            ],
        ], [
            'nama_jurusan.required' => 'Nama Jurusan wajib diisi.',
            'nama_jurusan.unique' => 'Nama Jurusan sudah ada.',
        ]);

        $jurusan->update($validatedData);

        // BARIS INI YANG HARUS DIUBAH (line 101 dalam kode Anda)
        // Dari 'jurusan.index' menjadi 'admin.jurusan.index'
        return redirect()->route('admin.jurusan.index')->with('success', 'Data jurusan berhasil diperbaharui!');
    }

    /**
     * Menghapus resource jurusan yang ditentukan dari penyimpanan.
     *
     * @param  \App\Models\Jurusan  $jurusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();

        // Juga pastikan rute setelah destroy jika Anda mengarahkan kembali ke index
        return back()->with('success', 'Data jurusan berhasil dihapus!');
    }
}