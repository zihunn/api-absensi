<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KhsController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\presensiDosenController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShareQrController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TodoController;
use App\Models\Perizinan;
use App\Models\presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/jadwal', [JadwalController::class, 'index']);
// Route::post('/move', [SearchController::class, 'store']);
// Route::get('/dosen', [JadwalController::class, 'getJadwalDosen']);
Route::get('search/dosen/{jadwal_id}', [SearchController::class, 'getDosenMk']);
Route::get('search/dosen', [SearchController::class, 'searchDosen']);
Route::get('search/upload', [SearchController::class, 'store']);
Route::get('krs/show', [KrsController::class, 'show']);
Route::get('khs', [KhsController::class, 'index']);
Route::get('khs/print', [KhsController::class, 'print']);



// Route::get('todo/{status}', [TodoController::class, 'index']);
// Route::post('todo/', [TodoController::class, 'create']);
// Route::put('todo/{id}', [TodoController::class, 'edit']);
// Route::delete('todo/{id}', [TodoController::class, 'destroy']);


// Route::get('perizinan', [PerizinanController::class, 'index']);
// Route::post('perizinan/readstatus', [PerizinanController::class, 'readStatus']);
// Route::post('perizinan', [PerizinanController::class, 'create']);
// Route::post('perizinan/approve/{id}', [PerizinanController::class, 'approve']);
// Route::get('perizinan/showmk/{npm}', [PerizinanController::class, 'showMk']);


Route::get('shareqr', [ShareQrController::class, 'show']);
Route::delete('shareqr/{id}', [ShareQrController::class, 'delete']);


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('/ubahpassword', [AuthController::class, 'changePassword']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/{npm}', [AuthController::class, 'update']);
        Route::get('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('absensi')->group(function () {
        Route::post('/', [AbsensiController::class, 'absen']);
        Route::get('/', [AbsensiController::class, 'index']);
        Route::get('/show', [AbsensiController::class, 'show']);
    });

    Route::prefix('perizinan')->group(function () {
        Route::get('/showmk/{npm}', [PerizinanController::class, 'showMk']);
        Route::get('/', [PerizinanController::class, 'index']);
        Route::post('/readstatus', [PerizinanController::class, 'readStatus']);
        Route::post('/', [PerizinanController::class, 'create']);
        Route::post('/approve/{id}', [PerizinanController::class, 'approve']);
        Route::delete('/{id}', [PerizinanController::class, 'delete']);
    });

    Route::prefix('todo')->group(function () {
        Route::get('/{status}', [TodoController::class, 'index']);
        Route::post('/', [TodoController::class, 'create']);
        Route::put('/{id}', [TodoController::class, 'edit']);
        Route::delete('/{id}', [TodoController::class, 'destroy']);
    });

    Route::prefix('jadwal')->group(function () {
        Route::get('/', [JadwalController::class, 'store']);
        Route::get('/dosen', [JadwalController::class, 'getJadwalDosen']);
    });

    Route::prefix('krs')->group(function () {
        Route::get('/', [KrsController::class, 'index']);
        Route::get('/cetak', [KrsController::class, 'cetak']);
    });

    Route::prefix('presensi')->group(function () {
        Route::post('/', [presensiDosenController::class, 'index']);
        Route::get('/', [presensiDosenController::class, 'store']);
        Route::post('/delete/{id}', [presensiDosenController::class, 'delete']);
        Route::get('/mhsw', [presensiDosenController::class, 'getPresensiMhsw']);
        Route::get('/mhsw/detail', [presensiDosenController::class, 'detailPresensiMhsw']);
    });

    Route::prefix('search')->group(function () {
        Route::post('/', [SearchController::class, 'search']);
    });

    Route::prefix('dosen')->group(function () {
        Route::post('/tambah', [DosenController::class, 'addDosen']);
        Route::get('/show/{dosenId}', [DosenController::class, 'show']);
        Route::get('/delete/{id}', [DosenController::class, 'destroy']);
    });
});
