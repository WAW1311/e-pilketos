<?php

use App\Http\Controllers\quickcounts;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Restcontroller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\SiswaController;
use App\Http\Controllers\Dashboard\HomePageController;
use App\Http\Controllers\Dashboard\VotePapperController;
use App\Http\Controllers\Dashboard\FingerprintController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
route::get('/', function() {
    return redirect()->route('dashboard');
});
route::get('/home', function() {
    return redirect()->route('dashboard');
});
route::get('/votecounts', [quickcounts::class, 'index'])->name('votecounts');

route::get('/admin/login',[LoginController::class,'authenticate'])->name('login')->middleware('guest');
route::post('/admin/login',[LoginController::class,'authenticate'])->name('login')->middleware('guest');
route::get('/admin/register',[RegisterController::class,'authenticate'])->name('register')->middleware('guest');
route::post('/admin/register',[RegisterController::class,'authenticate'])->name('register')->middleware('guest');
route::get('/admin/dashboard', [HomePageController::class, 'index'])->name('dashboard')->middleware('auth');
route::get('/admin/dashboard/kelola/siswa',[SiswaController::class, 'index'])->name('kelola_siswa')->middleware('auth');
// route::post('/admin/dashboard/kelola/siswa',[SiswaController::class, 'ShowData']);
route::get('/admin/dashboard/kelola/siswa/tambah',[SiswaController::class, 'InsertData'])->name('tambah_siswa_GET')->middleware('auth');
route::post('/admin/dashboard/kelola/siswa/tambah',[SiswaController::class, 'InsertData'])->name('tambah_siswa_POST')->middleware('auth');
route::get('/admin/dashboard/kelola/siswa/update',[SiswaController::class, 'UpdateData'])->name('update_siswa_GET')->middleware('auth');
route::post('/admin/dashboard/kelola/siswa/update',[SiswaController::class, 'UpdateData'])->name('update_siswa_POST')->middleware('auth');
route::post('/admin/dashboard/kelola/siswa/delete',[SiswaController::class, 'DeleteData'])->name('delete_siswa_POST')->middleware('auth');
route::get('/admin/dashboard/kelola/surat_suara',[VotePapperController::class, 'index'])->name('kelola_surat');
route::get('/admin/dashboard/kelola/surat_suara/tambah',[VotePapperController::class, 'InsertData'])->name('tambah_surat_GET')->middleware('auth');
route::post('/admin/dashboard/kelola/surat_suara/tambah',[VotePapperController::class, 'InsertData'])->name('tambah_surat_POST')->middleware('auth');
route::get('/admin/dashboard/kelola/surat_suara/update',[VotePapperController::class, 'UpdateData'])->name('update_surat_GET')->middleware('auth');
route::post('/admin/dashboard/kelola/surat_suara/update',[VotePapperController::class, 'UpdateData'])->name('update_surat_POST')->middleware('auth');
route::post('/admin/dashboard/kelola/surat_suara/delete',[VotePapperController::class, 'DeleteData'])->name('delete_surat_POST')->middleware('auth');
route::resource('fingerprint', FingerprintController::class)->middleware('auth')->except(['show']);

route::get('/logout',[LoginController::class, 'Logout'])->name('logout')->middleware('auth');

