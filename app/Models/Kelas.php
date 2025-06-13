<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Menentukan kolom-kolom yang bisa diisi secara massal (mass assignable).
    // Ini penting untuk keamanan, mencegah mass assignment vulnerability.
    protected $fillable = ['nama_kelas', 'guru_id', 'jurusan_id'];

    /**
     * Relasi many-to-one dengan model Guru.
     * Sebuah Kelas memiliki satu Guru (wali kelas atau guru kelas).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guru()
    {
        // Secara default, Laravel akan mencari foreign key 'guru_id' di tabel 'kelas'.
        // Parameter 'guru_id' secara eksplisit di sini sudah benar dan membantu kejelasan.
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi many-to-one dengan model Jurusan.
     * Sebuah Kelas termasuk dalam satu Jurusan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jurusan()
    {
        // Secara default, Laravel akan mencari foreign key 'jurusan_id' di tabel 'kelas'.
        // Tidak perlu menambahkan parameter kedua jika foreign key mengikuti konvensi Laravel.
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relasi one-to-many dengan model Siswa.
     * Sebuah Kelas memiliki banyak Siswa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siswas()
    {
        // Secara default, Laravel akan mencari foreign key 'kelas_id' di tabel 'siswas'.
        return $this->hasMany(Siswa::class);
    }

    /**
     * Relasi one-to-many dengan model Jadwal.
     * Sebuah Kelas memiliki banyak Jadwal (pelajaran).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jadwals()
    {
        // Secara default, Laravel akan mencari foreign key 'kelas_id' di tabel 'jadwals'.
        return $this->hasMany(Jadwal::class);
    }

    // Anda bisa menambahkan relasi lain di sini jika diperlukan,
    // misalnya untuk materi, tugas, dll.
}