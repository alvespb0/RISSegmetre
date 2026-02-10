<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExameController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\IntegracaoController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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


Route::middleware(['checkUserType:admin,dev,medico,tecnico'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [ExameController::class, 'index'])->name('exames.index');
    Route::get('/pacientes', function () { return view('pacientes.index'); })->name('pacientes.index');
    Route::get('/relatorios', function () { return view('relatorios.index'); })->name('relatorios.index');
    Route::get('/configuracoes', function () { return view('configuracoes.index'); })->name('configuracoes.index');
});

Route::middleware(['checkUserType:admin,dev'])->controller(UserController::class)->group(function() {
    Route::get('/usuarios', 'show')->name('usuarios.index');
    Route::get('/usuarios/edit/{id}', 'edit')->name('usuarios.edit');
    Route::post('/usuarios/edit/{id}', 'update')->name('usuarios.update');
    Route::post('/usuarios/delete', 'destroy')->name('usuarios.delete');
});

Route::middleware(['checkUserType:admin,dev'])->controller(RegisteredUserController::class)->group(function(){
    Route::get('register', 'create')->name('register');
    Route::post('register', 'store');
});

Route::middleware(['checkUserType:dev'])->controller(ApiController::class)->group(function () {
    Route::get('/dev/api-tokens', 'show')->name('dev.api-tokens');
    Route::post('/dev/api-tokens', 'store')->name('dev.api-tokens.store');
    Route::post('/dev/api-tokens/{id}/toggle', 'toggleActive')->name('dev.api-tokens.toggle');
});

Route::middleware(['checkUserType:dev'])->controller(IntegracaoController::class)->group(function () {
    Route::get('/dev/integracoes', 'index')->name('dev.integracoes.index');
    Route::get('/dev/integracoes/create', 'create')->name('dev.integracoes.create');
    Route::post('/dev/integracoes', 'store')->name('dev.integracoes.store');
    Route::get('/dev/integracoes/{integracao}/edit', 'edit')->name('dev.integracoes.edit');
    Route::put('/dev/integracoes/{integracao}', 'update')->name('dev.integracoes.update');
    Route::delete('/dev/integracoes/{integracao}', 'destroy')->name('dev.integracoes.destroy');
    Route::post('/dev/integracoes/{id}/restore', 'restore')->name('dev.integracoes.restore');
});

Route::middleware(['checkUserType:admin,dev,tecnico,medico'])->controller(ExameController::class)->group(function (){
    Route::get('/exames', 'index')->name('exames.index');
    Route::get('/exames/baixar-dicom/{idEnc}', 'getDicomFile')->name('baixar.dicom');
    Route::get('/exames/baixar-laudo/{idEnc}', 'getLaudoFile')->name('baixar.laudo');
    Route::get('/exames/baixar-protocolo/{idEnc}', 'getProtocoloFile')->name('baixar.protocolo');
});

Route::middleware(['checkPatientProtocol'])->controller(PatientController::class)->group(function (){
    Route::get('/exames/delivery-protocol', 'exames')->name('patient.exames');
    Route::get('/exames/delivery-protocol/download-laudo/{protocoloEnc}', 'downloadLaudo')->name('patient.download.laudo');
    Route::get('/exames/delivery-protocol/download-imagem/{idEnc}', 'downloadImagemJpg')->name('patient.download.imagem');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/update-signature', [ProfileController::class, 'updateSignature'])->name('profile.update-signature');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
