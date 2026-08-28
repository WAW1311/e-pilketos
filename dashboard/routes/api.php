<?php

use App\Http\Controllers\Restcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Autentikasi via API_TOKEN (Sanctum kiosk). Rate limit ditangani grup 'api' (per-IP).
    Route::post('/votepapper', [Restcontroller::class, 'Votepapper']);
    Route::post('/verify', [Restcontroller::class, 'VerifyNis']);
});

// Autentikasi via JWT voting hasil /verify (bukan API_TOKEN).
Route::middleware('voting.jwt')->group(function () {
    Route::post('/voting', [Restcontroller::class, 'SubmitVote']);
});
