<?php

namespace App\Http\Middleware;

use App\Services\VotingToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memvalidasi JWT voting (dari /api/verify) pada endpoint /api/voting.
 * Klaim nis & vote_id disematkan ke request agar tidak bisa dimanipulasi klien.
 */
class VerifyVotingToken
{
    public function __construct(private VotingToken $votingToken)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token voting tidak ditemukan.'], 401);
        }

        try {
            $claims = $this->votingToken->verify($token);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        // Identitas pemilih diambil dari token, bukan dari input klien.
        $request->attributes->set('voting_nis', $claims['nis']);
        $request->attributes->set('voting_vote_id', $claims['vote_id']);

        return $next($request);
    }
}
