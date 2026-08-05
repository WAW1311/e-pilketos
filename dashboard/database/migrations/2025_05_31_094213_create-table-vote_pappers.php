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
        Schema::create('vote_pappers', function (Blueprint $table) {
            $table->string('vote_id')->primary();
            $table->string('paslon1');
            $table->string('paslon2');
            $table->string('paslon3');
            $table->foreign('paslon1')->references('paslon_id')->on('paslons')->onDelete('cascade');
            $table->foreign('paslon2')->references('paslon_id')->on('paslons')->onDelete('cascade');
            $table->foreign('paslon3')->references('paslon_id')->on('paslons')->onDelete('cascade');
            $table->string('periode');
            $table->timestamp('dimulai')->nullable();
            $table->timestamp('berakhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_pappers');
    }
};
