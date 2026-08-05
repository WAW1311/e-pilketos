<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FingerprintModel;
use App\Models\SiswaModel;
use App\Models\VotePapper;
use Illuminate\Http\Request;

class FingerprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = FingerprintModel::with(['siswa', 'vote'])->get();
        // dd($data);
        return view('Fingerprint.index', [
            'title' => 'Kelola Sidik Jari',
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siswa = SiswaModel::all();
        $votepapper = VotePapper::all();
        return view('Fingerprint.create', [
            'title' => 'Tambah Sidik Jari',
            'siswa' => $siswa,
            'votepapper' => $votepapper
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa' => 'required',
            'vote_id' => 'required',
            'template' => 'required',
        ]);

        // dd($request->input('siswa'));

        FingerprintModel::create([
            'id_siswa' => $request->input('siswa'),
            'vote_id' => $request->input('vote_id'),
            'template' => $request->input('template'),
        ]);

        return redirect()->route('fingerprint.index')->with('success', 'Sidik jari berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FingerprintModel $fingerprint)
    {
        $siswa = SiswaModel::all();
        $votepapper = VotePapper::all();
        return view('Fingerprint.edit', [
            'title' => 'Edit Sidik Jari',
            'fingerprint' => $fingerprint,
            'siswa' => $siswa,
            'votepapper' => $votepapper
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FingerprintModel $fingerprint)
    {
        $request->validate([
            'siswa' => 'required|string',
            'vote_id' => 'required|string',
            'template' => 'required|string',
        ]);
        $fingerprint->update([
            'id_siswa' => $request->input('siswa'),
            'vote_id' => $request->input('vote_id'),
            'template' => $request->input('template'),
        ]);

        return redirect()->route('fingerprint.index')->with('success', 'Sidik jari berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FingerprintModel $fingerprint)
    {
        $fingerprint->delete();

        return redirect()->route('fingerprint.index')->with('success', 'Sidik jari berhasil dihapus.');
    }
}
