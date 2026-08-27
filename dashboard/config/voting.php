<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Voting Token (JWT)
    |--------------------------------------------------------------------------
    |
    | Token JWT jangka pendek yang diterbitkan saat /api/verify berhasil
    | (NIS matched & belum memilih), dipakai untuk mengautentikasi /api/voting.
    |
    */

    // Secret HMAC untuk menandatangani JWT. Fallback ke APP_KEY bila tidak diset.
    'jwt_secret' => env('APP_KEY'),

    // Masa berlaku token (detik). Default 15 menit.
    'jwt_ttl' => (int) env('VOTING_TOKEN_TTL', 900),
];
