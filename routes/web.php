<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JogoController;

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