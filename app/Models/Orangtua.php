<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orangtua extends Model
{
    use HasFactory;

    // Nama tabel secara eksplisit (sesuai dengan dump SQL Anda)
    protected $table = 'orangtuas';

    // Kolom yang dapat diisi secara massal (fillable)
    // PERBAIKAN: Tambahkan 'nama' ke array $fillable
    protected $fillable = ['user_id', 'nama', 'alamat', 'no_telp']; // <-- BARIS INI YANG DIUBAH

    /**
     * Relasi many-to-one dengan model User.
     * Seorang Orangtua hanya memiliki satu User (akun login).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Secara default, Laravel akan mencari foreign key 'user_id'
        // di tabel 'orangtuas' yang merujuk ke 'id' di tabel 'users'.
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-many dengan model Siswa.
     * Seorang Orangtua bisa memiliki banyak Siswa, dan seorang Siswa bisa memiliki banyak Orangtua.
     * Menggunakan tabel pivot 'orangtua_siswas'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function siswas()
    {
        // Parameter:
        // 1. Model target: Siswa::class
        // 2. Nama tabel pivot: 'orangtua_siswas'
        // 3. Foreign key model ini di tabel pivot: 'orangtua_id'
        // 4. Foreign key model target di tabel pivot: 'siswa_id'
        return $this->belongsToMany(Siswa::class, 'orangtua_siswas', 'orangtua_id', 'siswa_id');
    }
}