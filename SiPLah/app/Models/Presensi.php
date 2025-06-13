<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model // <-- PASTIKAN NAMA KELAS INI 'Presensi'
{
    use HasFactory;

    // Nama tabel jika tidak menggunakan konvensi Laravel (plural dari nama model)
    // Karena nama tabel Anda 'presensis', ini sudah sesuai konvensi.
    // protected $table = 'presensis'; // Anda bisa mengkomentari ini jika nama tabel sudah plural

    // Kolom yang dapat diisi secara massal (fillable)
    protected $fillable = [
        'siswa_id',
        'tanggal_presensi',
        'status_presensi',
        'keterangan',
        'jam_masuk',
        'jam_keluar',
        'mata_pelajaran_id',
        'guru_id',
    ];

    // Cast atribut ke tipe data tertentu (opsional, tapi bagus untuk tanggal/waktu)
    protected $casts = [
        'tanggal_presensi' => 'date',
        'jam_masuk' => 'datetime:H:i:s', // Format jam
        'jam_keluar' => 'datetime:H:i:s', // Format jam
    ];

    // Definisi Relasi

    // Presensi ini dimiliki oleh satu Siswa
    public function siswa()
    {
        // Pastikan 'Siswa' adalah nama model Anda untuk tabel 'siswas'
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Presensi ini mungkin terkait dengan satu Mata Pelajaran
    public function mapel()
    {
        // Pastikan 'Mapel' adalah nama model Anda untuk tabel 'mapels'
        return $this->belongsTo(Mapel::class, 'mata_pelajaran_id');
    }

    // Presensi ini mungkin dicatat oleh satu Guru
    public function guru()
    {
        // Pastikan 'Guru' adalah nama model Anda untuk tabel 'gurus'
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
