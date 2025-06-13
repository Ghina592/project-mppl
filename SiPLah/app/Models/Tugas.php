<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'deskripsi', 'kelas_id', 'guru_id', 'file', 'mapel_id', 'tanggal_batas'];

    // PERBAIKAN: Gunakan $casts untuk konversi tanggal ke datetime (Carbon instance)
    protected $casts = [
        'tanggal_batas' => 'datetime',
    ];

    public function guru() {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function jawaban() {
        return $this->hasMany(Jawaban::class, 'tugas_id');
    }
}
