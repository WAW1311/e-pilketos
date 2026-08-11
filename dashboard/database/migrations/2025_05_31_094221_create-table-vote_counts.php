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
        Schema::create('vote_counts', function (Blueprint $table) {
            $table->id();
            $table->string('vote_id');
            $table->string('paslon_id');
            $table->bigInteger('id_fp')->nullable();
            $table->foreign('vote_id')->references('vote_id')->on('vote_pappers')->onDelete('cascade');
            $table->foreign('paslon_id')->references('paslon_id')->on('paslons')->onDelete('cascade');
            // id_fp sengaja tanpa foreign key: tabel fingerprint_models baru dibuat
            // setelah migrasi ini, dan sesuai dump database kolom ini tidak ber-FK.
            // Kolom nis + unique(vote_id, nis) ditambahkan pada migrasi alter berikutnya.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_counts');
    }
};
