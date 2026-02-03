<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExameController;
use App\Http\Controllers\Api\MedicoController;

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

Route::middleware('apiBearer')->get('/exames', [ExameController::class, 'index'])->name('api.show.all-exames');
Route::middleware('apiBearer')->get('/exames/{id}', [ExameController::class, 'show'])->name('api.show-exame');
Route::middleware('apiBearer')->get('/exames/download-image/{instance_uuid}', [ExameController::class, 'downloadDicom'])->name('api.download-dicom');
Route::middleware('apiBearer')->post('/exames/laudar/{id}', [ExameController::class, 'setLaudo'])->name('api.set-laudo');

Route::middleware('apiBearer')->controller(MedicoController::class)->group(function(){
    Route::get('/medico', 'index')->name('api.show.all-medico');
    Route::get('/medico/{id}', 'show')->name('api.show-medico');
    Route::post('/medico/cadastrar', 'store')->name('api.store-medico');
    Route::put('/medico/update/{id}', 'update')->name('api.update-medico');
    Route::get('/medico/delete/{id}', 'destroy')->name('api.delete-medico');
    Route::get('/medico/restore/{id}', 'restore')->name('api.restore-medico');
});