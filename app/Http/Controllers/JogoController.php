<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;
use App\Models\Tabuleiro;
use App\Models\Casa;

class JogoController extends Controller
{
    public function index() {
        return view('index');
    }

    public function criarJogo() {
        $jogo = new Jogo();
        $jogo->save();

        $tabuleiros = TabuleiroController::criarTabuleiros($jogo);

        return view('posicionar-navios', compact('jogo'));
    }
}
