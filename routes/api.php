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
Route::middleware('apiBearer')->post('/medico/cadastrar', [MedicoController::class, 'store'])->name('api.store-medico');
Route::middleware('apiBearer')->get('/medico', [MedicoController::class, 'index'])->name('api.show.all-medico');
Route::middleware('apiBearer')->get('/medico/{id}', [MedicoController::class, 'show'])->name('api.show-medico');
Route::middleware('apiBearer')->put('/medico/update/{id}', [MedicoController::class, 'update'])->name('api.update-medico');
Route::middleware('apiBearer')->get('/medico/delete/{id}', [MedicoController::class, 'destroy'])->name('api.delete-medico');