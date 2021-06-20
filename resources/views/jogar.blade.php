@extends('layouts.app')

@section('content')

    <div id="container"></div>

    <div id="casasTabuleiroCOM">
        @foreach ($jogo->tabuleiros[1]->casas as $casa)
            <input type="hidden" id="casaCOM{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->id}}">
            <input type="hidden" id="casaCOMPreenchido{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->preenchido}}">
            <input type="hidden" id="casaCOMAcertado{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->acertado}}">
            <input type="hidden" id="casaCOMNavio{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->navio_id}}">
            <input type="hidden" id="casaCOMPosicao{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->posicao_do_navio}}">
        @endforeach
    </div>

    <div id="naviosTabuleiroCOM">
        @foreach ($jogo->tabuleiros[1]->navios as $i => $navio)
            <input type="hidden" id="navio{{$i+1}}" value="{{$navio->id}}">
            <input type="hidden" id="tamanho_navio{{$i+1}}" value="{{$navio->tamanho}}">
        @endforeach
    </div>

    <script>
        var telaLargura = 930;
        var telaAltura = 615;
        var navios = {//aqui defini onde os navios vao spawnar na tela e a posicao
        };
        var casas = {//object pra guardar as casas
        };
        const tamanhoTabuleiro = 10;
        const espacoEntreCasas = 55; //relativo ao tamanho em pixels da celula
        const espacoDeEncaixe = 28; //pra encaixar o navio na celula
        const anguloDeRotacao = 270; //rotacao de click no navio
        const quantidadeNavios = 5;

        var stage = new Konva.Stage({//stage padrao pra jogar os elementos na tela
            container: 'container',
            width: telaLargura,
            height: telaAltura,
        });

        var navioLayer = new Konva.Layer();
        var images = {};

        var naviosSources = {//source de onde fica os navios
            navio1: '{{asset('img/navios/portaaviao.png')}}',
            navio2: '{{asset('img/navios/guerra.png')}}',
            navio3: '{{asset('img/navios/encouracado.png')}}',
            navio4: '{{asset('img/navios/encouracado.png')}}',
            navio5: '{{asset('img/navios/submarino.png')}}',
        };

        function getNavioSource(nav){
            switch(nav.tamanho){
                case 6:
                    return 'navio1';
                case 4:
                    return 'navio2';
                case 3:
                    return 'navio3';
                case 1:
                    return 'navio5';
            }
        };

        function loadImages(sources, callback) { //carrega as imagens definidas em sources e as propriedades de initStage
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
        };

        function loadNavioAfundado(sourceNavio, navio, cas){
            images[sourceNavio] = new Image();
            images[sourceNavio].onload = function(){
                navio.image(images[sourceNavio]);
                navio.rotation(getRotacaoNavio(cas));
            }
            stage.add(navioLayer);
            images[sourceNavio].src = naviosSources[sourceNavio];
        }

        function initNaviosCasas(){
            for(let i = 1; i <= tamanhoTabuleiro; i++){ //criacao das casas
                for(let j = 1; j <= tamanhoTabuleiro; j++){
                    casas['casa'+j+'x'+i] = {x: espacoEntreCasas*i, y: espacoEntreCasas*j, linha: j, coluna: i, ocupada: document.getElementById('casaCOMPreenchido'+j+'x'+i).value, navio: document.getElementById('casaCOMNavio'+j+'x'+i).value, id: document.getElementById('casaCOM'+j+'x'+i).value, posicao: document.getElementById('casaCOMPosicao'+j+'x'+i).value, acertado: false,}; //cria as casas dando espaco e nome unico
                }
            };
            for(let i = 1; i <= quantidadeNavios; i++){
                navios['navio'+i] = {x: espacoEntreCasas*(tamanhoTabuleiro+1), y: (espacoEntreCasas*(i+1)), vida: parseInt(document.getElementById('tamanho_navio'+i).value), tamanho: parseInt(document.getElementById('tamanho_navio'+i).value), id: document.getElementById('navio'+i).value};
            };
        };

        function getCasa(nav){
            for(let key in casas){
                if(casas[key].ocupada == "1"){
                    if(nav.id == casas[key].navio){
                        let cas = casas[key];
                        if(cas.posicao == 1 && getRotacaoNavio(cas) == 0){
                            return cas;
                        }else{
                            if(cas.posicao == nav.tamanho && getRotacaoNavio(cas) == 270){
                                return cas;
                            }
                        }
                    }
                }
            }
        };


        function getNavio(cas){
            for(let key in navios){
                if(cas.navio == navios[key].id){
                    return navios[key];
                }
            }
        };

        function getNavioTamanho(cas){
            let nav = getNavio(cas);
            return nav.tamanho;
        };

        function getNavioKonva(nav){
            let imagesNavios = stage.find('.navio');
            for(let key in imagesNavios){
                navio = imagesNavios[key];
                let navtemp = navios[navio.id()];
                if(navtemp.id == nav.id){
                    return navio;
                }
            }
        };

        function getRotacaoNavio(cas){
            let nav = getNavio(cas);
            for(let key in casas){
                let casatemp = casas[key];
                if(casatemp.navio == cas.navio){
                    if(nav.tamanho == 1){
                        return 0;
                    }
                    if(casatemp.linha !=  cas.linha){
                        return 270;
                    }
                    if(casatemp.coluna != cas.coluna){
                        return 0;
                    }
                }
            }
        };

        function setNaviosPosicoes(){
            for(let key in navios){
                let cas = getCasa(navios[key]);
                let angulo = getRotacaoNavio(cas);
                if(angulo == 0){
                    navios[key].x = cas.x+5;
                    navios[key].y = cas.y+5;
                }else{
                    navios[key].x = cas.x+5;
                    navios[key].y = cas.y+espacoDeEncaixe+15;
                }
            }
        };

        function navioAtingido(cas){
            let nav = getNavio(cas);
            nav.vida -= 1;
        };

        function afundarNavio(cas){
            let nav = getNavio(cas);
            if(nav.vida == 0){
                loadNavioAfundado(getNavioSource(nav), getNavioKonva(nav), cas);
            }
        };

        function initStageNavios() {
            for (let key in navios) {
                (function () {
                    let nav = navios[key];
                    let navio = new Konva.Image({
                        x: nav.x,
                        y: nav.y,
                        name: 'navio',
                        id: ''+key,
                    });
                    navioLayer.add(navio);
                })();
            }
            stage.add(navioLayer);
        };

        function initStageCasas(images){
            var casaLayer = new Konva.Layer();
            for (let key in casas) {
                (function () {
                    let imageObj = images[key];
                    let cas = casas[key];

                    let casa = new Konva.Image({
                        x: cas.x,
                        y: cas.y,
                        image: imageObj,
                    });

                    casa.on('mouseover', function (evt) {
                        if(!cas.acertado){
                            document.body.style.cursor = 'pointer';
                            casa.image(images['cell_board_mouseover']);
                        }
                    });

                    casa.on('mouseout', function (evt) {
                        if(!cas.acertado){
                            document.body.style.cursor = 'default';
                            casa.image(images[key]);
                        }
                    });

                    casa.on('click', function() {
                        if(!cas.acertado){
                            cas.acertado = true;
                            if(cas.ocupada == "1"){
                                casa.image(images['cell_board_bomb']);
                                navioAtingido(cas);
                                afundarNavio(cas);
                            }else{
                                casa.image(images['cell_board_water']);
                            }
                        }else{
                            alert("Casa já acertada");
                        }
                    });

                    casaLayer.add(casa);
                })();
            }
            stage.add(casaLayer);
        };



      var casasSources = {
          cell_board_mouseover: '{{asset('img/cell_board_mouseover.png')}}',
          cell_board_bomb: '{{asset('img/cell_board_bomb.png')}}',
          cell_board_water: '{{asset('img/cell_board_water.png')}}',
      };

        for(let i = 1; i <= tamanhoTabuleiro; i++){//cria um source pra cada casa e coloca em sources
            for(let j = 1; j <= tamanhoTabuleiro; j++){
                casasSources['casa'+i+'x'+j] = '{{asset('img/cell_board.png')}}';
            }
        };

        initNaviosCasas();
        setNaviosPosicoes();
        loadImages(casasSources, initStageCasas);//carrega o stage pra iniciar os bagulhos
        initStageNavios();

    </script>
@endsection
