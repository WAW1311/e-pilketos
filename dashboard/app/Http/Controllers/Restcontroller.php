<?php

namespace App\Http\Controllers;

use App\Events\VoteCounts;
use App\Models\Paslon;
use App\Models\VoteCount;
use App\Models\SiswaModel;
use App\Models\VotePapper;
use App\Events\Fingerprint;
use Illuminate\Http\Request;
use InvalidArgumentException;
use App\Models\FingerprintModel;
use App\Events\MatchingFingerprint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Restcontroller extends Controller
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

    public function Votepapper(Request $request)
    {
        $data = $this->votePapper->with(
            'paslonFirst.ketua',
            'paslonFirst.wakil',
            'paslonSecond.ketua',
            'paslonSecond.wakil',
            'paslonThird.ketua',
            'paslonThird.wakil',
        )->where('vote_id', $request->query('vote_id'))->firstOrFail();
        $data = json_decode(json_encode($data));
        if (!$data) {
            return response()->json([
                'message' => 'Surat suara tidak ditemukan',
                'data' => [],
                'assets' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Surat suara ditemukan',
            'data' => $data,
            'assets' => [
                'paslon1' => asset('storage/' . $data->paslon_first->asset),
                'paslon2' => asset('storage/' . $data->paslon_second->asset),
                'paslon3' => asset('storage/' . $data->paslon_third->asset),
            ]
        ], 200);
    }

    /**
     *
     * 
     */
    public function VerifyNis(Request $request)
    {
        $validated = $request->validate([
            'vote_id' => 'required|string',
            'nis' => 'required|string',
        ]);

        $votePapper = VotePapper::where('vote_id', $validated['vote_id'])->first();
        if (!$votePapper) {
            return response()->json([
                'message' => 'Surat suara tidak ditemukan',
                'decision' => 'invalid_vote',
            ], 404);
        }

        $result = $this->evaluateVoter($votePapper, $validated['nis']);

        return response()->json([
            'message' => $result['message'],
            'decision' => $result['decision'],
            'data' => $result['decision'] === 'matched'
                ? ['nis' => $validated['nis'], 'nama' => $result['nama']]
                : null,
        ], $result['status']);
    }

    public function SubmitVote(Request $request)
    {
        $validated = $request->validate([
            'vote_id' => 'required|string',
            'nis' => 'required|string',
            'paslon_id' => 'required|string',
        ]);

        try {
            $data = DB::transaction(function () use ($validated) {
                $votePapper = VotePapper::where('vote_id', $validated['vote_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (now()->lt($votePapper->dimulai) || now()->gt($votePapper->berakhir)) {
                    throw new \DomainException('Voting sedang tidak dibuka');
                }

                $eligibility = $this->evaluateVoter($votePapper, $validated['nis']);
                if ($eligibility['decision'] !== 'matched') {
                    throw new \DomainException($eligibility['message']);
                }

                $validPaslonIds = [$votePapper->paslon1, $votePapper->paslon2, $votePapper->paslon3];
                if (!in_array($validated['paslon_id'], $validPaslonIds, true)) {
                    throw new \DomainException('Kandidat tidak valid untuk surat suara ini');
                }

                return $this->votecount->create([
                    'vote_id' => $validated['vote_id'],
                    'nis' => $validated['nis'],
                    'paslon_id' => $validated['paslon_id'],
                ]);
            });

            broadcast(new VoteCounts($data));

            return response()->json([
                'message' => 'Vote successfully recorded',
                'data' => [
                    'vote_id' => $data->vote_id,
                    'paslon_id' => $data->paslon_id,
                ]
            ], 200);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to record vote',
            ], 500);
        }
    }

    /**
     *
     *
     * @return array{decision:string,message:string,status:int,nama:?string}
     */
    private function evaluateVoter(VotePapper $votePapper, string $nis): array
    {
        $siswa = SiswaModel::where('nis', $nis)->first();
        if (!$siswa) {
            return [
                'decision' => 'not_found',
                'message' => 'NIS tidak terdaftar',
                'status' => 404,
                'nama' => null,
            ];
        }

        if (in_array($nis, $this->candidateNisForVote($votePapper), true)) {
            return [
                'decision' => 'is_candidate',
                'message' => 'Kandidat tidak diperbolehkan memilih',
                'status' => 403,
                'nama' => $siswa->nama,
            ];
        }

        $alreadyVoted = VoteCount::where('vote_id', $votePapper->vote_id)
            ->where('nis', $nis)
            ->exists();
        if ($alreadyVoted) {
            return [
                'decision' => 'already_voted',
                'message' => 'NIS sudah pernah memilih pada surat suara ini',
                'status' => 409,
                'nama' => $siswa->nama,
            ];
        }

        return [
            'decision' => 'matched',
            'message' => 'Verifikasi berhasil',
            'status' => 200,
            'nama' => $siswa->nama,
        ];
    }

    /**
     *
     *
     * @return array<int,string>
     */
    private function candidateNisForVote(VotePapper $votePapper): array
    {
        $paslonIds = array_filter([
            $votePapper->paslon1,
            $votePapper->paslon2,
            $votePapper->paslon3,
        ]);

        return Paslon::whereIn('paslon_id', $paslonIds)
            ->get(['ketua', 'wakil'])
            ->flatMap(fn ($p) => [$p->ketua, $p->wakil])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

}
