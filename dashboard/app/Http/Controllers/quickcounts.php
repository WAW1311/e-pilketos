<?php

namespace App\Http\Controllers;

use App\Models\VoteCount;
use App\Models\SiswaModel;
use App\Models\VotePapper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\VotePapperController;

class quickcounts extends Controller
{
    protected $votecounts;
    protected $siswa;

    protected $votePapperController;

    protected $votePapper;

    public function __construct($datavotecounts = new VoteCount(), $datasiswa = new SiswaModel(), $votepapper = new VotePapperController())
    {
        $this->siswa = $datasiswa;
        $this->votecounts = $datavotecounts;
        $this->votePapperController = $votepapper;
        $this->votePapper = new VotePapper();
    }
    public function index(Request $request)
    {
        $data = $this->votePapper
            ->with([
                'paslonFirst.ketua',
                'paslonFirst.wakil',
                'paslonSecond.ketua',
                'paslonSecond.wakil',
                'paslonThird.ketua',
                'paslonThird.wakil',
                'votecounts',
            ])
            ->where('vote_id', $request->query('vote_id'))
            ->firstOrFail();

        $voteMap = $data->votecounts
            ->groupBy('paslon_id')
            ->map(fn($items) => $items->count());

        $paslons = collect([$data->paslonFirst, $data->paslonSecond, $data->paslonThird])
            ->filter();
        $paslonList = $paslons->map(function ($p) use ($voteMap) {
            $ketua = $p->getRelation('ketua');
            $wakil = $p->getRelation('wakil');
            return [
                'paslon_id' => $p->paslon_id,
                'nomor'     => (int) $p->nomor,
                'asset'     => $p->asset,
                'ketua'     => $ketua->nama,
                'wakil'     => $wakil->nama,
                'vote'      => (int) ($voteMap[$p->paslon_id] ?? 0),
            ];
        })->sortBy('nomor')->values();
        // dd($paslonList);
        return view('quickcounts', [
            'title'       => 'Perhitungan Suara',
            'vote_id'     => $data->vote_id,
            'periode'     => $data->periode,
            'totalSiswa'  => $this->siswa->count() ?? 0,
            'totalSuara'  => $data->votecounts->count() ?? 0,
            'tanggalTutup' => $data->berakhir,
            'paslonList'  => $paslonList,
        ]);
    }
}
