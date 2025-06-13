<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Nama tabel secara eksplisit jika tidak mengikuti konvensi plural (misalnya, jika tabelnya 'guru' bukan 'gurus')
    // Berdasarkan dump SQL Anda, nama tabelnya adalah 'gurus', jadi ini sesuai konvensi Laravel.
    // Jika Anda ingin lebih eksplisit atau jika nama tabel Anda adalah 'guru' (singular), Anda bisa un-comment baris ini:
    protected $table = 'gurus';

    // Tambahkan 'user_id' ke fillable
    protected $fillable = ['nip', 'nama', 'mapel_id', 'no_telp', 'alamat', 'foto', 'user_id'];

    /**
     * Relasi many-to-one dengan model Mapel.
     * Seorang Guru dimiliki oleh satu Mata Pelajaran (bidang ajar).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    /**
     * Relasi many-to-one dengan model User.
     * Seorang Guru hanya memiliki satu User (akun login).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many dengan model Presensi.
     * Seorang Guru bisa mencatat banyak catatan Presensi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presensis()
    {
        // Parameter kedua ('guru_id') adalah nama foreign key di tabel 'presensis'
        // yang merujuk ke primary key 'id' di tabel 'gurus' (model ini).
        return $this->hasMany(Presensi::class, 'guru_id');
    }
}
