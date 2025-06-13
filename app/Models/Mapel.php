<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    // Nama tabel secara eksplisit jika tidak mengikuti konvensi plural (misalnya, jika tabelnya 'mapel' bukan 'mapels')
    // Berdasarkan dump SQL Anda, nama tabelnya adalah 'mapels', jadi ini sesuai konvensi Laravel.
    // Jika Anda ingin lebih eksplisit atau jika nama tabel Anda adalah 'mapel' (singular), Anda bisa un-comment baris ini:
    protected $table = 'mapels';

    protected $fillable = ['nama_mapel', 'jurusan_id'];

    /**
     * Relasi many-to-one dengan model Jurusan.
     * Satu Mata Pelajaran dimiliki oleh satu Jurusan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relasi one-to-many dengan model Presensi.
     * Satu Mata Pelajaran bisa memiliki banyak catatan Presensi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presensis()
    {
        // Parameter kedua ('mata_pelajaran_id') adalah nama foreign key di tabel 'presensis'
        // yang merujuk ke primary key 'id' di tabel 'mapels' (model ini).
        return $this->hasMany(Presensi::class, 'mata_pelajaran_id');
    }
}