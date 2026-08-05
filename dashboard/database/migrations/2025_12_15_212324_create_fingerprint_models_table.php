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
        Schema::create('fingerprint_models', function (Blueprint $table) {
            $table->id();
            $table->string('id_siswa');
            $table->string('vote_id');
            $table->text('template');
            $table->foreign('id_siswa')->references('nis')->on('siswa_models')->onDelete('cascade');
            $table->foreign('vote_id')->references('vote_id')->on('vote_pappers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fingerprint_models');
    }
};
