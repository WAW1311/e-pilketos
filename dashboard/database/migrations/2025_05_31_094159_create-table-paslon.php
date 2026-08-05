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
        Schema::create('paslons', function (Blueprint $table) {
            $table->string('paslon_id')->primary();
            $table->string('ketua');
            $table->string('wakil');
            $table->foreign('ketua')->references('nis')->on('siswa_models')->onDelete('cascade');
            $table->foreign('wakil')->references('nis')->on('siswa_models')->onDelete('cascade');
            $table->string('nomor');
            $table->string('asset');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paslons');
    }
};
