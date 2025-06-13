<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->biginteger('user_id')->unsigned()->nullable(); // user_id bisa nullable
            $table->string('nis')->unique(); // NIS seharusnya unik
            $table->string('nama');
            $table->string('telp')->nullable(); // Dibuat nullable
            $table->string('alamat')->nullable(); // Dibuat nullable
            $table->string('foto')->nullable();
            $table->bigInteger('kelas_id')->unsigned()->nullable(); // Dibuat nullable
            $table->timestamps();

            // Relation Tables
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siswas');
    }
}
