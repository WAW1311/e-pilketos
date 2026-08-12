<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PenanggungJawab as PenanggungJawabModel;
use Illuminate\Http\Request;

class PenanggungJawab extends Controller
{
    /**
     * Daftar jabatan penanggung jawab yang tetap.
     * Nilai harus cocok dengan enum kolom `jabatan` pada migration.
     */
    private const JABATAN = [
        'kepala_sekolah',
        'waka_kesiswaan',
        'ketua_kpu',
        'sekretaris_kpu',
    ];

    /**
     * Simpan/perbarui data penanggung jawab.
     *
     * Setiap jabatan bersifat single row — memakai updateOrCreate agar
     * data lama diperbarui, bukan ditambah, saat disimpan ulang.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'penanggung_jawab' => 'required|array',
            'penanggung_jawab.*.nama' => 'nullable|string|max:255',
            'penanggung_jawab.*.nomor_induk' => 'nullable|string|max:255',
        ]);

        try {
            foreach (self::JABATAN as $jabatan) {
                $row = $validated['penanggung_jawab'][$jabatan] ?? null;
                if ($row === null) {
                    continue;
                }

                PenanggungJawabModel::updateOrCreate(
                    ['jabatan' => $jabatan],
                    [
                        'nama' => $row['nama'] ?? '',
                        'nomor_induk' => $row['nomor_induk'] ?? '',
                    ]
                );
            }

            return redirect()->route('berita_acara')->with('success', 'Data penanggung jawab berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data penanggung jawab!');
        }
    }
}
