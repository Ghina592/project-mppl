<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles', // Kolom 'roles' ada di tabel 'users' Anda
        'nis',   // Kolom 'nis' ada di tabel 'users' Anda
        'nip'    // Kolom 'nip' ada di tabel 'users' Anda
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi one-to-one dengan model Guru.
     * Seorang User dapat memiliki satu data Guru yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guru()
    {
        // Laravel akan mencari 'user_id' di tabel 'gurus'
        return $this->hasOne(Guru::class);
    }

    /**
     * Relasi one-to-one dengan model Siswa.
     * Seorang User dapat memiliki satu data Siswa yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function siswa()
    {
        // Laravel akan mencari 'user_id' di tabel 'siswas'
        return $this->hasOne(Siswa::class);
    }

    /**
     * Relasi one-to-one dengan model Orangtua.
     * Seorang User dapat memiliki satu data Orangtua yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orangtua()
    {
        // Laravel akan mencari 'user_id' di tabel 'orangtuas'
        return $this->hasOne(Orangtua::class);
    }

    /**
     * Helper untuk memeriksa apakah user memiliki role tertentu.
     * Asumsi kolom 'roles' di tabel 'users' menyimpan string role tunggal.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles === $role;
    }
}