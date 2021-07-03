<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JogoController;
use App\Http\Controllers\CasaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [JogoController::class, 'index'])->name('index');
Route::get('/novo-jogo', [JogoController::class, 'criarJogo'])->name('jogo.new');
Route::post('/salvar-navios', [JogoController::class, 'salvarNaviosUser'])->name('save.navios');
Route::get('/jogar/{id}', [JogoController::class, 'jogar'])->name('jogar');

Route::get('/checar-acerto', [CasaController::class, 'checarTiro'])->name('atirar');

Route::get('/resultado/{resultado}', [JogoController::class, 'resultado'])->name('resultado');
