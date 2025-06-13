<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengaturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mencari pengaturan yang sudah ada, atau membuat yang baru jika belum ada
        $pengaturan = Pengaturan::first();

        // Jika tidak ada entri pengaturan, buat yang baru dengan nilai default
        if (is_null($pengaturan)) {
            $pengaturan = Pengaturan::create([
                'name' => 'Nama Sekolah Default', // Ganti dengan nama sekolah default yang Anda inginkan
                'logo' => null, // Anda bisa set path logo default jika ada, atau biarkan null
            ]);
        }

        return view('pages.admin.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_sekolah.required' => 'Nama sekolah harus diisi.',
            'nama_sekolah.string' => 'Nama sekolah harus berupa teks.',
            'nama_sekolah.max' => 'Nama sekolah tidak boleh lebih dari 255 karakter.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat jpeg, png, jpg, atau gif.',
            'logo.max' => 'Logo tidak boleh lebih dari 2MB.',
        ]);

        $pengaturan = Pengaturan::findOrFail($id);
        $pengaturan->name = $validatedData['nama_sekolah'];

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada dan file-nya benar-benar ada di storage
            if ($pengaturan->logo && Storage::exists(Str::after($pengaturan->logo, 'storage/'))) {
                Storage::delete(Str::after($pengaturan->logo, 'storage/'));
            }
            // Simpan logo baru dengan nama sesuai nama sekolah
            $slug = Str::slug($pengaturan->name);
            $newLogoName = $slug . '_logo.' . $request->file('logo')->getClientOriginalExtension();
            $path = $request->file('logo')->storeAs('logos', $newLogoName, 'public');
            $pengaturan->logo = 'storage/' . $path; // Menyimpan path lengkap untuk akses publik
        }

        $pengaturan->save();

        return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }
}
