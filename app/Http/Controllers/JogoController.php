<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;

class JogoController extends Controller
{
    public function index() {
        return view('index');
    }

    public function criarJogo() {
        $jogo = new Jogo();
        $jogo->save();

        return view('posicionar-navios', compact('jogo'));
    }
}
