<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use Illuminate\Support\Facades\Auth;

class TugasAnakController extends Controller
{
    public function index()
    {
        $anak = auth()->user()->anak; // pastikan relasi user->anak ada

        // Ambil semua tugas
        $tugas = \App\Models\Tugas::all();

        // Ambil semua jawaban tugas anak
        $jawabanAnak = \App\Models\Jawaban::where('anak_id', $anak->id)->get()->keyBy('tugas_id');

        return view('orangtua.tugas-anak', compact('tugas', 'jawabanAnak', 'anak'));
    }
}
