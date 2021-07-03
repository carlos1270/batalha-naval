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

    public function salvarNaviosUser(Request $request) {
        $jogo = Jogo::find($request->jogo_id);
        
        $casas_ids = $request->casas_id;
        $navios_ids = $request->navio_id;
        $posicoes_ints = $request->posicoes_id;

        foreach ($navios_ids as $i => $id) {
            if ($id != null) {
                $casa = Casa::find($casas_ids[$i]);
                $casa->preenchido = true;
                $casa->navio_id = $id;
                $casa->posicao_do_navio = $posicoes_ints[$i];
                $casa->update();
            }
        }

        $tabuleiros = $jogo->tabuleiros;

        return redirect( route('jogar', ['id' => $jogo->id]) );
    }

    public function jogar($id) {
        $jogo = Jogo::find($id);
        
        return view('jogar', compact('jogo'));
    }

    public function resultado($resultado) {
        if ($resultado == 1) {
            $img = asset('img/ganhamo.png');
            $msg_1 = "Você ganhou!";
            $msg_2 = "Parabéns soldado, você ganhou a batalha!";
        } else {
            $img = asset('img/perdemo.jpeg');
            $msg_1 = "Você perdeu!";
            $msg_2 = "Não foi dessa vez, eles podem ter vencido a batalha, mas com treino você vencerá a guerra.";
        }
        
        return view('resultado', compact('img', 'msg_1', 'msg_2'));
    }
}
