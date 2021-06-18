<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Casa;
use App\Models\Navio;
use App\Models\Tabuleiro;

class CasaController extends Controller
{
    public const ERROU = 310;
    public const ACERTOU = 311;
    public const AFUNDOU = 312;
    public const GANHOU = 313;

    public function checarTiro(Request $request) {
        $casa = Casa::find($request->id);
        $casa->acertado = true;
        $casa->update();

        $codigoRetorno = $this::ERROU;
        $msg = "Acertou na água";

        if ($casa->preenchido) {
            $codigoRetorno = $this::ACERTOU;
            $msg = "Acertou";

            $navio = $casa->navio;
            if($this->checarAfundado($navio)) {
                $codigoRetorno = $this::AFUNDOU;
                $msg = "Afundou";

                $tabuleiro = $casa->tabuleiro;
                if ($this->checarGanhou($tabuleiro)) {
                    $codigoRetorno = $this::GANHOU;
                    $msg = "Ganhou";
                }
            }
        } 

        return response($msg, $codigoRetorno);
    }

    public function checarAfundado(Navio $navio) {
        $casas = $navio->casas;

        $count = 0;
        foreach ($casas as $casa) {
            if ($casa->acertado) {
                $count++;
            }
        }

        if ($count == $casas->count()) {
            $navio->afundado = true;
            $navio->update();
            return true;
        }
        return false;
    }

    public function checarGanhou(Tabuleiro $tabuleiro) {
        $navios = $tabuleiro->navios;
        $count = 0;

        foreach ($navios as $navio) {
            if ($navio->afundado) {
                $count++;
            }
        }

        if ($count == $navios->count()) {
            return true;
        }   
        return false;
    }
}
