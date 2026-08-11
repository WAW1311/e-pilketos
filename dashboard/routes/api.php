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
    Route::post('/votepapper', [Restcontroller::class, 'Votepapper']);
    Route::post('/verify', [Restcontroller::class, 'VerifyNis']);
    Route::post('/voting', [Restcontroller::class, 'SubmitVote']);
});

Route::get('/foo', function () {
    $target = realpath('dashboard/storage/app/public');
    $shortcut = 'storage';

    if (file_exists($shortcut)) {
        return 'Symlink sudah ada.';
    }

    symlink($target, $shortcut);

    return 'Symlink berhasil dibuat dari ' . $target . ' ke ' . $shortcut;
});

route::post('/fingerprint/store', [Restcontroller::class, 'FpReceive']);
// route::post('/fingerprint/match', [Restcontroller::class, 'FpMatched']);
