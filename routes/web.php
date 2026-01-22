<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExameController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/pacientes', function () {
        return view('pacientes.index');
    })->name('pacientes.index');
    Route::get('/relatorios', function () {
        return view('relatorios.index');
    })->name('relatorios.index');
    Route::get('/usuarios', function () {
        return view('usuarios.index');
    })->name('usuarios.index');
    Route::get('/configuracoes', function () {
        return view('configuracoes.index');
    })->name('configuracoes.index');
});

Route::middleware(['checkUserType:admin,dev,tecnico,medico'])->controller(ExameController::class)->group(function (){
    Route::get('/exames', 'index')->name('exames.index');
    Route::get('/exames/baixar-dicom/{id}', 'getDicomFile')->name('baixar.dicom');
    Route::get('/exames/baixar-laudo/{id}', 'getLaudoFile')->name('baixar.laudo');
    Route::get('/exames/baixar-protocolo/{id}', 'getProtocoloFile')->name('baixar.protocolo');
});

Route::middleware(['checkPatientProtocol'])->controller(PatientController::class)->group(function (){
    Route::get('/exames/delivery-protocol', 'exames')->name('patient.exames');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/update-signature', [ProfileController::class, 'updateSignature'])->name('profile.update-signature');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
