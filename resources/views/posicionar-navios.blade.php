@extends('layouts.app')

@section('content')


<!DOCTYPE html>
<html>
  <head>
    <script src="https://unpkg.com/konva@8.0.4/konva.min.js"></script>
    <meta charset="utf-8" />
    <title>Batalha Naval Posicionamento</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #f0f0f0;
      }
    </style>
  </head>
  <body>
    <div id="container"></div>

    <div id="casas">
        @foreach ($jogo->tabuleiros[0]->casas as $casa)
            <input type="hidden" id="casa{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->id}}">
        @endforeach
    </div>

    <form id="" method="POST" action="">
        <input type="hidden" name="jogo_id" value="{{$jogo->id}}">
        @foreach ($jogo->tabuleiros[0]->casas as $casa)
            <div id="casa_{{$casa->id}}" style="display: none;">
                <input type="text" name="casas_id[]" value="{{$casa->id}}">
                <input type="text" name="navio_id[]" value="">
            </div>
        @endforeach
    </form>

    <script>
      var telaLargura = 720;
      var telaAltura = 720;
      const tamanhoTabuleiro = 10;
      const espacoEntreCasas = 55; //relativo ao tamanho em pixels da celula mudar em PHP Codigo
      const espacoDeEncaixe = 28; //pra encaixar o navio na celula
      const anguloDeRotacao = 270; //rotacao de click no navio

      function loadImages(sources, callback) { //carrega as imagens definidas em sources e as propriedades de initStage
        var images = {};
        var loadedImages = 0;
        var numImages = 0;
        for (let src in sources) {
          numImages++;
        }
        for (let src in sources) {
          images[src] = new Image();
          images[src].onload = function () {
            if (++loadedImages >= numImages) {
              callback(images);
            }
          };
          images[src].src = sources[src];
        }
      }

      function getCasasProximo(navio, casas){//Pega a casa mais proxima do navio pra fazer o encaixe, se existir uma
          let selecionada = [];
          for (var casa in casas) {
            let a = navio;
            let o = casas[casa];
            let ax = a.x();
            let ay = a.y();
            if(navio.rotation() == anguloDeRotacao){
              if(ax > (o.x-espacoDeEncaixe) && ax < (o.x+espacoDeEncaixe) && ay-espacoDeEncaixe > (o.y-espacoDeEncaixe) && ay-espacoDeEncaixe < (o.y+espacoDeEncaixe)){
                selecionada.push(casa);
              }
            }else{
              if(ax > (o.x-espacoDeEncaixe) && ax < (o.x+espacoDeEncaixe) && ay > (o.y-espacoDeEncaixe) && ay < (o.y+espacoDeEncaixe)){
                selecionada.push(casa);
              }
            }
          }
          return selecionada;
      }

      function getLinhaCasa(casa){
        return casa.linha;
      }

      function getColunaCasa(casa){
        return casa.coluna;
      }

      function foraDaTela(navio){
        if(navio.x() > telaLargura-50 || navio.x() < 0 || navio.y() > telaAltura-50 || navio.y() < 0){
          return true;
        }else{
          return false;
        }
      }

      function initStage(images) {//inicializa as imagens
        var stage = new Konva.Stage({//stage padrao pra jogar os elementos na tela
          container: 'container',
          width: telaLargura,
          height: telaAltura,
        });
        var navioLayer = new Konva.Layer();
        var navioShapes = [];

        var navios = {//aqui defini onde os navios vao spawnar na tela e a posicao
          navio: {
            x: 600,
            y: 70,
            tamanho: 2,
            posicionado: false,
          },
        };

        var casas = {//object pra guardar as casas
        };

        for(let i = 1; i <= tamanhoTabuleiro; i++){ //criacao das casas
          for(let j = 1; j <= tamanhoTabuleiro; j++){
            casas['casa'+j+'x'+i] = {x: espacoEntreCasas*i, y: espacoEntreCasas*j, linha: j, coluna: i, id: document.getElementById('casa'+j+'x'+i).value}; //cria as casas dando espaco e nome unico

          }
        };

        for (var key in casas) {//iterar sobre os objects casas pra adicionar a imagem relacionada e a posicao
          (function () {
            var imageObj = images[key];
            var cas = casas[key];

            var casa = new Konva.Image({
              image: imageObj,
              x: cas.x,
              y: cas.y,
            });

            navioLayer.add(casa);
          })();
        }

        for (var key in navios) {//faz o mesmo pros navios, itera sobre eles e cria o objeto do tipo Image do Konva pra colocar o navio
          (function () {
            var privKey = key;
            var nav = navios[key];

            var navio = new Konva.Image({
              image: images[key],
              x: nav.x,
              y: nav.y,
              draggable: true,
            });

            navio.on('dragstart', function () {
              this.moveToTop();
            });

            navio.on('dragend', function () { //função pra quando arrastar, fazer o encaixe certinho
              let resultado = getCasasProximo(navio, casas);
              if (!resultado.length == 0){
                  let casa = casas[resultado[0]];
                  if (!navio.inRightPlace) {
                    console.log(casa);
                    if(navio.rotation() == 0){//essa variação aqui é por causa que depende se o navio ta em uma posicao diferente
                      navio.position({
                        x: casa.x+(5),
                        y: casa.y+(5),
                      });
                    }else{
                      navio.position({
                        x: casa.x+(5),
                        y: casa.y+espacoDeEncaixe+15,
                      });
                    }
                  }
                }
              if (foraDaTela(navio)){
                navio.position({
                  x: 600,
                  y: 70,
                });
              }
            });
            navio.on('mouseout', function () {
              navio.image(images[privKey]);
              document.body.style.cursor = 'default';
            });

            navio.on('dragmove', function () {
              document.body.style.cursor = 'pointer';
            });

            navio.on('click', function() {//clicar nele gira o bagulho :)
              if(navio.rotation() == anguloDeRotacao){
                navio.rotation(0)
                navioLayer.draw();
              }else{
                navio.rotation(anguloDeRotacao);
                navioLayer.draw();
              };
            });

            navioLayer.add(navio);
            navioShapes.push(navio);
          })();
        }

        stage.add(navioLayer);
      }

      var sources = {//source de onde fica os navios
        navio: '{{asset('img/navios/navioS.png')}}',
      };

      for(let i = 1; i <= tamanhoTabuleiro; i++){//cria um source pra cada casa e coloca em sources
          for(let j = 1; j <= tamanhoTabuleiro; j++){
            sources['casa'+i+'x'+j] = '{{asset('img/cell_board.png')}}';
          }
      };

      loadImages(sources, initStage);//carrega o stage pra iniciar os bagulhos
    </script>
  </body>
</html>
@endsection
