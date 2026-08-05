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
            $table->bigInteger('id_fp');
            $table->foreign('vote_id')->references('vote_id')->on('vote_pappers')->onDelete('cascade');
            $table->foreign('paslon_id')->references('paslon_id')->on('paslons')->onDelete('cascade');
            $table->foreign('id_fp')->references('id')->on('fingerprint_models')->onDelete('cascade');
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
