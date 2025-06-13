<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas; 
use App\Models\Materi;
use App\Models\Siswa;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Response; 

class MateriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Pastikan eager loading mapel untuk guru agar tidak N+1 query jika digunakan di view
        $guru = Guru::where('user_id', Auth::user()->id)->first();

        if (is_null($guru)) {
            // Tangani kasus jika user guru tidak memiliki record Guru terkait
            \Log::error('MateriController@index: User ID ' . Auth::id() . ' dengan peran guru tidak memiliki record Guru terkait.');
            Auth::logout();
            return redirect('/login')->with('error', 'Profil guru Anda tidak ditemukan. Silakan hubungi administrator.');
        }

        $materi = Materi::where('guru_id', $guru->id)->get();
        // Pastikan eager loading mapel untuk jadwal jika digunakan di view
        $jadwal = Jadwal::where('mapel_id', $guru->mapel_id)->get();

        // Ambil semua data kelas dari database
        $kelas = Kelas::all(); 

        // Teruskan data kelas ke view bersama dengan data lainnya
        return view('pages.guru.materi.index', compact('materi', 'jadwal', 'guru', 'kelas')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // abor(404); // Diasumsikan create via modal. Tetap seperti ini jika modal dipicu dari index
        // Jika Anda ingin method create() menampilkan form di halaman terpisah, Anda akan butuh ini:
        // $kelas = Kelas::all();
        // return view('pages.guru.materi.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Pastikan guru ada sebelum mengakses propertinya
        $guru = Guru::where('user_id', Auth::user()->id)->first();
        if (is_null($guru)) {
            return redirect()->back()->with('error', 'Data guru Anda tidak ditemukan.');
        }

        $this->validate($request, [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kelas_id' => 'required|exists:kelas,id',
            'file' => 'required|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048',
        ]);

        $filePath = null; 
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $namaFile = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('file/materi', $namaFile, 'public'); 
        }

        $materi = new Materi;
        $materi->guru_id = $guru->id;
        // --- PERBAIKAN DI SINI ---
        // Tambahkan mapel_id dari guru yang sedang login
        // Diasumsikan kolom mapel_id ada di tabel `gurus`
        if (!is_null($guru->mapel_id)) {
            $materi->mapel_id = $guru->mapel_id;
        } else {
            // Tangani jika guru tidak memiliki mapel_id (misal: log error, set default, atau abort)
            \Log::error('MateriController@store: Guru ID ' . $guru->id . ' tidak memiliki mapel_id.');
            return redirect()->back()->with('error', 'Profil guru Anda tidak terkait dengan mata pelajaran.');
        }
        // --- AKHIR PERBAIKAN ---
        $materi->kelas_id = $request->kelas_id;
        $materi->judul = $request->judul;
        $materi->deskripsi = $request->deskripsi;
        $materi->file = $filePath; 
        $materi->save(); // BARIS 96

        return redirect()->route('materi.index')->with('success', 'Materi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $kelas = Kelas::all(); 
        $materi = Materi::findOrFail($id);

        return view('pages.guru.materi.edit', compact('materi', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Materi $materi) 
    {
        $this->validate($request, [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kelas_id' => 'required|exists:kelas,id', 
            'file' => 'nullable|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:2048', 
        ]);

        $materi->judul = $request->judul;
        $materi->deskripsi = $request->deskripsi;
        $materi->kelas_id = $request->kelas_id;

        // Jika ada mapel_id di tabel materi dan perlu di-update:
        // $guru = Guru::where('user_id', Auth::user()->id)->first();
        // if ($guru && !is_null($guru->mapel_id)) {
        //     $materi->mapel_id = $guru->mapel_id;
        // }

        if ($request->hasFile('file')) {
            if ($materi->file && Storage::disk('public')->exists($materi->file)) {
                Storage::disk('public')->delete($materi->file);
            }
            $file = $request->file('file');
            $namaFile = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('file/materi', $namaFile, 'public'); 
            $materi->file = $filePath; 
        }

        $materi->save(); 

        return redirect()->route('materi.index')->with('success', 'Data materi berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $materi = Materi::find($id);

        if (!$materi) {
            return back()->with('error', 'Materi tidak ditemukan.');
        }

        if ($materi->file && Storage::disk('public')->exists($materi->file)) {
            Storage::disk('public')->delete($materi->file);
        }

        $materi->delete();
        return redirect()->route('materi.index')->with('success', 'Data materi berhasil dihapus');
    }

    /**
     * Menampilkan materi untuk peran Siswa.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function siswa()
    {
        $user = Auth::user()->load('siswa.kelas');
        $siswa = $user->siswa;

        if (is_null($siswa)) {
            \Log::error('MateriController@siswa: User ID ' . Auth::id() . ' dengan peran siswa tidak memiliki record Siswa terkait.');
            Auth::logout();
            return redirect('/login')->with('error', 'Profil siswa Anda tidak ditemukan. Silakan hubungi administrator.');
        }

        $kelas = $siswa->kelas;
        if (is_null($kelas)) {
            \Log::warning('MateriController@siswa: Siswa ID ' . $siswa->id . ' tidak memiliki record Kelas terkait (kelas_id mungkin NULL atau tidak valid).');
            return view('pages.siswa.materi.index', compact('siswa'))
                         ->with('error_message', 'Anda belum memiliki kelas yang ditugaskan untuk melihat materi.');
        }

        $materi = Materi::with(['guru.mapel'])->where('kelas_id', $kelas->id)->get();

        $guru = null;
        if (!is_null($kelas->guru_id)) { 
            $guru = Guru::find($kelas->guru_id);
        }

        if (is_null($guru)) {
            \Log::warning('MateriController@siswa: Kelas ID ' . $kelas->id . ' tidak memiliki guru terkait (guru_id mungkin NULL atau tidak valid).');
            $guru = (object)['nama' => 'Tidak Ada Guru', 'mapel' => (object)['nama_mapel' => '-']];
        }

        return view('pages.siswa.materi.index', compact('materi', 'guru', 'kelas'));
    }

    /**
     * Mengunduh file materi.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $file = Materi::findOrFail($id);
        
        if (!Storage::disk('public')->exists($file->file)) {
            abort(404, 'File materi tidak ditemukan atau telah dihapus.');
        }
        
        $path = Storage::disk('public')->path($file->file);
        return Response::download($path);
    }
}
