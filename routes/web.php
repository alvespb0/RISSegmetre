<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExameController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['checkUserType:admin,dev,medico,tecnico'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/exames', [ExameController::class, 'index'])->name('exames.index');
    
    // Rotas placeholder para outras páginas
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

Route::middleware(['checkUserType:admin,dev,tecnico'])->controller(ExameController::class)->group(function (){
    Route::post('/exames/salvar-anamnese', '')->name('exames.anamnese');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
