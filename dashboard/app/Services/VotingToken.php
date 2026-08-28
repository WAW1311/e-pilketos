<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

/**
 * Menerbitkan & memverifikasi JWT jangka pendek untuk endpoint /api/voting.
 * Token terikat pada pasangan (nis, vote_id) dari hasil verifikasi NIS.
 */
class VotingToken
{
    private const ALG = 'HS256';

    private function secret(): string
    {
        $secret = (string) config('voting.jwt_secret');
        if ($secret === '') {
            throw new \RuntimeException('Voting token secret belum dikonfigurasi.');
        }
        return $secret;
    }

    /**
     * Terbitkan token untuk pemilih yang sudah terverifikasi.
     */
    public function issue(string $nis, string $voteId): string
    {
        $now = time();
        $ttl = (int) config('voting.jwt_ttl', 900);

        $payload = [
            'iss' => 'epilketos',
            'aud' => 'voting',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
            'jti' => bin2hex(random_bytes(16)),
            'nis' => $nis,
            'vote_id' => $voteId,
        ];

        return JWT::encode($payload, $this->secret(), self::ALG);
    }

    /**
     * Verifikasi token & kembalikan klaimnya. Melempar exception bila tidak valid.
     *
     * @return array{nis:string,vote_id:string}
     */
    public function verify(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret(), self::ALG));
        } catch (ExpiredException $e) {
            throw new \DomainException('Token voting kedaluwarsa, silakan verifikasi ulang.');
        } catch (SignatureInvalidException $e) {
            throw new \DomainException('Token voting tidak valid.');
        } catch (\Throwable $e) {
            throw new \DomainException('Token voting tidak valid.');
        }

        if (($decoded->aud ?? null) !== 'voting' || empty($decoded->nis) || empty($decoded->vote_id)) {
            throw new \DomainException('Token voting tidak valid.');
        }

        return [
            'nis' => (string) $decoded->nis,
            'vote_id' => (string) $decoded->vote_id,
        ];
    }
}
