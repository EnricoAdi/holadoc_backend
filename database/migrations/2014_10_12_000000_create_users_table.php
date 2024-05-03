<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id("id_user");
            $table->string('username')->unique();
            $table->string('name');
            $table->string('password');
            $table->integer('role')->default(0); //0 dokter, 1 pasien
            $table->string("spesialis");
            $table->string("deskripsi");
            $table->integer("tahun_praktek"); //max 2023
            $table->integer("biaya_konsultasi")->default(0);
            $table->integer("total_pendapatan")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
