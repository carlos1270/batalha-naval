<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tabuleiro;
use App\Models\Navio;

class NavioController extends Controller
{
    public static function criarNavios(Tabuleiro $tabuleiro) {
        $navios = [6,4,3,3,1];
        foreach ($navios as $tamanho){
            $navio = new Navio();
            $navio->tamanho = $tamanho;
            $navio->afundado = FALSE;
            $navio->tabuleiro_id = $tabuleiro->id;
            $navio->save();
        }
    }
}
