<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JogoController extends Controller
{
    public function index() {
        return view('index');
    }

    public function criarJogo() {
        dd('programar conteúdo do método');
    }
}
