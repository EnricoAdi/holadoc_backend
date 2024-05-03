<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table relasi user dokter - user pasien
     */
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id("id_pasien");
            $table->string("keluhan");
            $table->integer("biaya_konsultasi");
            $table->integer("id_user_dokter")->index();
            $table->integer("id_user_pasien")->index();
            $table->integer("accepted")->default(0); // 0 = diterima, -1 = ditolak, 1 = selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
