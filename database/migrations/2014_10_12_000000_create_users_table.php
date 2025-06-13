<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('roles'); // Admin, Guru, Siswa, Orangtua
            $table->rememberToken();
            // Jadikan nis dan nip unique. Mereka bisa null untuk user yang tidak memiliki keduanya (misal: admin, ortu)
            $table->string('nis')->unique()->nullable(); // NIS harus unik
            $table->string('nip')->unique()->nullable(); // NIP harus unik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}