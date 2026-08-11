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

    /**
     * Verifikasi kelayakan pemilih berdasarkan NIS.
     *
     * Urutan cek: NIS terdaftar -> bukan kandidat pada votepapper ini ->
     * belum pernah memilih pada votepapper ini.
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
                // Kunci baris votepapper agar cek kelayakan & insert bersifat atomik.
                $votePapper = VotePapper::where('vote_id', $validated['vote_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Validasi ulang di server — jangan percaya klien.
                $eligibility = $this->evaluateVoter($votePapper, $validated['nis']);
                if ($eligibility['decision'] !== 'matched') {
                    throw new \DomainException($eligibility['message']);
                }

                // Pastikan paslon yang dipilih benar-benar milik votepapper ini.
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
            return response()->json([
                'message' => 'Failed to record vote',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Evaluasi kelayakan seorang NIS terhadap sebuah votepapper.
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
     * Daftar NIS ketua & wakil dari seluruh paslon pada votepapper ini.
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
