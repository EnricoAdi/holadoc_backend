<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Dokter\ChatDokterController;
use App\Http\Controllers\Dokter\HomeDokterController;
use App\Http\Controllers\Pasien\ChatPasienController;
use App\Http\Controllers\Pasien\HomePasienController;
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

Route::get('/', function(){
    return response()->json([
        "message" => "Welcome to Holadoc Backend"
    ]);
});
Route::prefix('auth')->group(function () {
    Route::post("/register/pasien",[RegisterController::class,"RegisterPasien"])->name("register-pasien");
    Route::post("/register/dokter",[RegisterController::class,"RegisterDokter"])->name("register-dokter");
    Route::post("/login",LoginController::class)->name("login");
    Route::post("/refresh",RefreshTokenController::class)->name("refresh-token");
    Route::post("/logout",LogoutController::class)->name("logout");
});
Route::prefix('dokter')->middleware("auth:api")->group(function () {
    Route::get("/profile",[HomeDokterController::class,"GetProfile"])->name("dokter-profile");
    Route::put("/biaya",[HomeDokterController::class,"SetBiaya"])->name("dokter-biaya");
    Route::delete("/pasien/{id_pasien}",[HomeDokterController::class,"DeletePasien"])->name("dokter-delete-pasien");

    Route::get("/chat/{id_pasien}",[ChatController::class,"FetchChat"])->name("dokter-fetchchat");
    Route::put("/chat",[ChatController::class,"SendChatDokter"])->name("dokter-sendchat");
});
Route::prefix('pasien')->middleware("auth:api")->group(function () {
    Route::get("/doctors",[HomePasienController::class,"GetDoctors"])->name("pasien-getdoctors");
    Route::post("/create",[HomePasienController::class,"CreateAppointment"])->name("pasien-create");
    Route::get("/done/{id_pasien}",[HomePasienController::class,"DoneAppointment"])->name("pasien-done");

    Route::get("/chat",[HomePasienController::class,"GetRecentAppointmentPasien"])->name("pasien-fetchchat");
    Route::put("/chat",[ChatController::class,"SendChatPasien"])->name("pasien-sendchat");
});
