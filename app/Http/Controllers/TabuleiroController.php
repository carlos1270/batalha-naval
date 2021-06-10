<?php

namespace App\Http\Controllers;

use App\Models\Navio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Jogo;
use App\Models\Tabuleiro;
use App\Models\Casa;
use phpDocumentor\Reflection\Types\True_;

class TabuleiroController extends Controller
{
    public static function criarTabuleiros(Jogo $jogo) {
        $tabuleiros = collect();

        $tabuleiroJogador = new Tabuleiro();
        $tabuleiroJogador->qtd_linhas = 10;
        $tabuleiroJogador->qtd_colunas = 10;
        $tabuleiroJogador->qtd_jogadas = 0;
        $tabuleiroJogador->jogo_id = $jogo->id;
        $tabuleiroJogador->save();
        Casa::criarCasas($tabuleiroJogador);
        Navio::criarNavios($tabuleiroJogador);
        $tabuleiros->push($tabuleiroJogador);

        $tabuleiroCOM = new Tabuleiro();
        $tabuleiroCOM->qtd_linhas = 10;
        $tabuleiroCOM->qtd_colunas = 10;
        $tabuleiroCOM->qtd_jogadas = 0;
        $tabuleiroCOM->jogo_id = $jogo->id;
        $tabuleiroCOM->save();
        $tabuleiros->push($tabuleiroCOM);
        Casa::criarCasas($tabuleiroCOM);
        Navio::criarNavios($tabuleiroCOM);

        self::preencherTabuleiroCOM($tabuleiroCOM);
        return $tabuleiros;

    }

    public static function escolherDirecao()
    {
        // 0 = HORIZONTAL e 1 = VERTICAL
        return rand(0,1);
    }

    public static function cabeNavio($tamanhoNavio, $coluna){
        if(($tamanhoNavio-1) + $coluna <= 10){
            return True;
        }
        return False;
    }

    public static function temCasasDisponiveisHorizontal($tamanhoNavio, $linha, $coluna, $tabuleiro){
        $cont = 0;
        $tamanho = ($tamanhoNavio-1) + $coluna;
        for($i=$coluna; $i <= $tamanho; $i++){
            $casa = $tabuleiro->casas()->where([['linha',$linha], ['coluna', $i]])->first();
            if(!$casa->preenchido){
                $cont +=1;
            }
        }
        if($cont==$tamanhoNavio){
            return True;
        }
        return False;
    }

    //posicao_do_navio = parte dele
    public static function preencherCasasHorizontal($navio, $linha, $coluna, $tabuleiro){
        $tamanho = ($navio->tamanho-1) + $coluna;
        $cont = 1;
        for($i=$coluna; $i <= $tamanho; $i++){
            $casa = $tabuleiro->casas()->where([['linha',$linha], ['coluna', $i]])->first();
            $casa->preenchido = True;
            $casa->posicao_do_navio = $cont;
            $cont = $cont + 1;
            $casa->navio_id = $navio->id;
            $casa->update();
        }
    }

    public static function temCasasDisponiveisVertical ($tamanhoNavio, $linha, $coluna, $tabuleiro){
        $cont = 0;
        $tamanho = ($tamanhoNavio-1) + $linha;
        for($i=$linha; $i <= $tamanho; $i++){
            $casa = $tabuleiro->casas()->where([['linha',$i], ['coluna', $coluna]])->first();
            if(!$casa->preenchido){
                $cont +=1;
            }
        }
        if($cont==$tamanhoNavio){
            return True;
        }
        return False;
    }

    public static function preencherCasasVertical($navio, $linha, $coluna, $tabuleiro){
        $tamanho = ($navio->tamanho-1) + $linha;
        $cont = 1;
        for($i=$linha; $i <= $tamanho; $i++){
            $casa = $tabuleiro->casas()->where([['linha',$i], ['coluna', $coluna]])->first();
            $casa->preenchido = True;
            $casa->posicao_do_navio = $cont;
            $cont = $cont + 1;
            $casa->navio_id = $navio->id;
            $casa->update();
        }
    }

    public static function preencherTabuleiroCOM(Tabuleiro $tabuleiro)
    {
        $naviosPC = $tabuleiro->navios;
        foreach ($naviosPC as $navio){
            $achou = True;
            $preencheu= True;
            $linhas = [1,2,3,4,5,6,7,8,9,10];
            $colunas = [1,2,3,4,5,6,7,8,9,10];
            if(self::escolherDirecao() == 0) {
                while ($preencheu) {
                    $posicaoLinha = array_rand($linhas,1);
                    while(count($colunas) != 0 && $achou) {
                        $posicaoColuna = array_rand($colunas,1);
                        if (self::cabeNavio($navio->tamanho, $colunas[$posicaoColuna])) {
                            if (self::temCasasDisponiveisHorizontal($navio->tamanho, $linhas[$posicaoLinha], $colunas[$posicaoColuna], $tabuleiro)) {
                                self::preencherCasasHorizontal($navio, $linhas[$posicaoLinha], $colunas[$posicaoColuna], $tabuleiro);
                                $preencheu = False;
                                $achou = False;
                            }
                        }
                        array_splice($colunas, $posicaoColuna, 1);
                    }
                    $colunas = [1,2,3,4,5,6,7,8,9,10];
                    array_splice($linhas, $posicaoLinha, 1);

                }

            }else {
                while($preencheu){
                    $posicaoColuna = array_rand($colunas,1);
                    while(count($linhas) != 0 && $achou) {
                        $posicaoLinha = array_rand($linhas,1);
                        if (self::cabeNavio($navio->tamanho, $linhas[$posicaoLinha])) {
                            if (self::temCasasDisponiveisVertical($navio->tamanho, $linhas[$posicaoLinha], $colunas[$posicaoColuna], $tabuleiro)) {
                                self::preencherCasasVertical($navio, $linhas[$posicaoLinha], $colunas[$posicaoColuna], $tabuleiro);
                                $preencheu = False;
                                $achou = False;
                            }
                        }
                        array_splice($linhas, $posicaoLinha, 1);
                    }
                    $linhas = [1,2,3,4,5,6,7,8,9,10];
                    array_splice($colunas, $posicaoColuna, 1);
                }

            }
        }

    }
}
