<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Verifikasi pemilih beralih dari sidik jari ke NIS. Kolom id_fp dilepas
     * dari fingerprint (dibuat nullable, tanpa FK) dan kolom nis ditambahkan
     * sebagai identitas pemilih, dengan unique (vote_id, nis) sebagai pengaman
     * anti-vote-ganda di level database.
     *
     * Catatan: FK vote_counts_id_fp_foreign mungkin tidak pernah terbentuk
     * karena vote_counts dibuat sebelum tabel fingerprint_models, jadi drop-nya
     * dijaga agar hanya dijalankan bila constraint benar-benar ada.
     */
    public function up(): void
    {
        $this->dropForeignIfExists('vote_counts', 'vote_counts_id_fp_foreign');

        if (Schema::hasColumn('vote_counts', 'id_fp')) {
            Schema::table('vote_counts', function (Blueprint $table) {
                $table->dropColumn('id_fp');
            });
        }

        Schema::table('vote_counts', function (Blueprint $table) {
            if (!Schema::hasColumn('vote_counts', 'nis')) {
                $table->string('nis')->nullable()->after('vote_id');
            }
            $table->bigInteger('id_fp')->nullable()->after('paslon_id');

            $table->foreign('nis')->references('nis')->on('siswa_models')->onDelete('cascade');
            $table->unique(['vote_id', 'nis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_counts', function (Blueprint $table) {
            $table->dropUnique(['vote_id', 'nis']);
            $table->dropForeign(['nis']);
            $table->dropColumn(['nis', 'id_fp']);
        });

        Schema::table('vote_counts', function (Blueprint $table) {
            $table->bigInteger('id_fp')->after('paslon_id');
        });
    }

    /**
     * Drop foreign key hanya jika constraint dengan nama tersebut ada.
     */
    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            Schema::table($table, function (Blueprint $t) use ($constraint) {
                $t->dropForeign($constraint);
            });
        }
    }
};
