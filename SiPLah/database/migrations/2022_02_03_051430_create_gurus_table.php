<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGurusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->biginteger('user_id')->unsigned()->nullable(); // user_id bisa nullable
            $table->string('nama');
            $table->string('nip')->unique(); // NIP seharusnya unik
            $table->bigInteger('mapel_id')->unsigned()->nullable(); // Dibuat nullable
            $table->string('no_telp')->nullable(); // Dibuat nullable
            $table->string('alamat')->nullable(); // Dibuat nullable
            $table->string('foto')->nullable();
            $table->timestamps();

            // Relation tabels
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gurus');
    }
}
