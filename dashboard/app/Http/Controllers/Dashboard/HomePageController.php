<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Paslon;
use App\Models\VoteCount;
use App\Models\SiswaModel;
use App\Models\VotePapper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    protected $paslon;
    protected $votePapper;
    protected $siswa;

    protected $votecount;

    public function __construct($datapaslon = new Paslon(), $datavotePapper = new VotePapper(), $datasiswa = new SiswaModel(), $votecount = new VoteCount())
    {
        $this->paslon = $datapaslon;
        $this->votePapper = $datavotePapper;
        $this->siswa = $datasiswa;
        $this->votecount = $votecount;
    }
    public function index()
    {
        $datavote = Votecount::with('votepapper')->get()->groupBy(function ($item) {
            return $item->votepapper->periode ?? 'Unknown';
        })->map(function ($grouped) {
            return $grouped->count();
        });
        $data = [
            'title' => 'Dashboard Panitia',
            'siswa' => $this->siswa->count(),
            'suratSuara' => $this->votePapper->count(),
            'paslon' => $this->paslon->count(),
            'labels' => $datavote->keys()->values()->all(),
            'data' => $datavote->values()->all(),
        ];
        return view('index', $data);
    }
}
