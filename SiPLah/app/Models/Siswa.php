<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Nama tabel secara eksplisit (sesuai konvensi Laravel, nama plural)
    protected $table = 'siswas';

    // Kolom yang dapat diisi secara massal
    protected $fillable = ['nis', 'nama', 'kelas_id', 'telp', 'alamat', 'foto', 'user_id'];

    /**
     * Relasi many-to-one dengan model Kelas.
     * Seorang Siswa hanya memiliki satu Kelas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi many-to-many dengan model Orangtua.
     * Seorang Siswa bisa memiliki banyak Orangtua, dan seorang Orangtua bisa memiliki banyak Siswa.
     * Menggunakan tabel pivot 'orangtua_siswas'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orangtua()
    {
        return $this->belongsToMany(Orangtua::class, 'orangtua_siswas', 'siswa_id', 'orangtua_id');
    }

    /**
     * Relasi many-to-one dengan model User.
     * Seorang Siswa hanya memiliki satu User (akun login).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many dengan model Presensi.
     * Seorang Siswa bisa memiliki banyak catatan Presensi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }
}