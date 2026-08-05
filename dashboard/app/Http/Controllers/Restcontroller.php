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
use Illuminate\Support\Facades\Http;
use Phpml\Math\Distance\Manhattan;

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

    public function SubmitVote(Request $request)
    {
        $vote_id = $request->query('vote_id');
        $paslon_id = $request->query('paslon_id');
        $id_fp = $request->query('id_fp');
        try {
            $data = $this->votecount->create([
                'vote_id' => $vote_id,
                'id_fp' => $id_fp,
                'paslon_id' => $paslon_id
            ]);
            broadcast(new VoteCounts($data));
            return response()->json([
                'message' => 'Vote successfully recorded',
                'data' => [
                    'vote_id' => $vote_id,
                    'paslon_id' => $paslon_id
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to record vote',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function FpReceive(Request $request)
    {
        $scanB64 = $request->json('template_base64');

        if (!$scanB64) {
            return response()->json([
                'message' => 'template_base64 is required'
            ], 422);
        }

        broadcast(new Fingerprint($scanB64));

        $manhattan = new Manhattan();

        $scanVector = $this->base64ToVector($scanB64);

        $dbTemplates = FingerprintModel::with('siswa')->get();

        $bestMatch = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach ($dbTemplates as $template) {

            if (!$template->template_base64) {
                continue;
            }

            $dbVector = $this->base64ToVector(
                $template->template_base64
            );

            if (count($scanVector) !== count($dbVector)) {
                continue;
            }

            $distance = $manhattan->distance(
                $scanVector,
                $dbVector
            );

            if ($distance < $bestDistance) {
                $bestDistance = $distance;

                $bestMatch = [
                    'id_fp' => $template->id,
                    'distance' => $distance,
                    'siswa' => $template->siswa,
                ];
            }
        }

        if (!$bestMatch) {
            return response()->json([
                'message' => 'No comparable fingerprint found'
            ], 404);
        }

        $threshold = 5000;

        $bestMatch['matched'] = $bestDistance <= $threshold;

        $alreadyVoted = VoteCount::where(
            'id_fp',
            $bestMatch['id_fp']
        )->exists();

        if (!$bestMatch['matched']) {
            $bestMatch['decision'] = 'unmatched';
        } elseif ($alreadyVoted) {
            $bestMatch['decision'] = 'already_voted';
        } else {
            $bestMatch['decision'] = 'matched';
        }

        broadcast(new MatchingFingerprint($bestMatch));

        return response()->json([
            'message' => 'Fingerprint distance completed',
            'best_match' => $bestMatch,
        ], 200);
    }


    private function base64ToVector(string $base64): array
    {
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64, 2)[1];
        }

        $binary = base64_decode($base64, true);

        if ($binary === false) {
            throw new \InvalidArgumentException(
                'Invalid fingerprint Base64'
            );
        }

        return array_values(
            unpack('C*', $binary)
        );
    }
}
