@extends('layouts.jogar-template')

@section('content')
    <div class="titulo-jogador-vs-com">
        <div class="titulo-jogador">
            <h1>JOGADOR</h1>
        </div>

        <div class="titulo-vs">
            <h1>VS</h1>
        </div>

        <div class="titulo-com">
            <h1>COM</h1>
        </div>
    </div>

    <div class="img-background">
        <div class="img-wrap">
            <div id="img">
                <img src={{asset('img/battle2.jpg')}} alt="battle">
            </div>
        </div>
    </div>

    <div id="container" class="cont"></div>

    <div id="casasTabuleiroCOM">
        @foreach ($jogo->tabuleiros[1]->casas as $casa)
            <input type="hidden" id="casaCOM{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->id}}">
        @endforeach
    </div>

    <div id="casasTabuleiroPlayer">
        @foreach ($jogo->tabuleiros[0]->casas as $casa)
            <input type="hidden" id="casaPlayer{{$casa->linha}}x{{$casa->coluna}}" value="{{$casa->id}}">
        @endforeach
    </div>

    <div id="naviosTabuleiroCOM">
        @foreach ($jogo->tabuleiros[1]->navios as $i => $navio)
            <input type="hidden" id="navioCOM{{$i+1}}" value="{{$navio->id}}">
            <input type="hidden" id="tamanho_navioCOM{{$i+1}}" value="{{$navio->tamanho}}">
        @endforeach
    </div>

    <div id="naviosTabuleiroPlayer">
        @foreach ($jogo->tabuleiros[0]->navios as $i => $navio)
            <input type="hidden" id="navioPlayer{{$i+1}}" value="{{$navio->id}}">
            <input type="hidden" id="tamanho_navioPlayer{{$i+1}}" value="{{$navio->tamanho}}">
        @endforeach
    </div>

    <audio id="background-sound" controls loop muted autoplay hidden>
        <source src="{{asset('audio/suspense02.mp3')}}" type="audio/mpeg">
    </audio>

    <button onclick="enableMute()">Som</button>

    <!-- Modal
    <div class="modal fade" id="ModalGanhou" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Você Ganhou !!!
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.href='index.blade.php'">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>-->

    <script>
        var telaLargura = 1380;
        var telaAltura = 800;
        var naviosCOM = {//aqui defini onde os navios vao spawnar na tela e a posicao
        };
        var naviosPlayer = {
        };
        var casasCOM = {//object pra guardar as casas
        };
        var casasPlayer = {
        };
        var vezDoJogador = true;
        var casasDisponiveis = []; //para o COM jogar
        var ganhou = false;
        var bgaudio = document.getElementById("background-sound");

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
        var casaLayer = new Konva.Layer();
        var images = {};

        var naviosSources = {//source de onde fica os navios
            navio1: '{{asset('img/navios/portaaviao.png')}}',
            navio2: '{{asset('img/navios/guerra.png')}}',
            navio3: '{{asset('img/navios/encouracado.png')}}',
            navio4: '{{asset('img/navios/encouracado.png')}}',
            navio5: '{{asset('img/navios/submarino.png')}}',
        };

        var sonsEspeciais = {
            somAgua: new Howl({
                src: [
                    '{{asset('audio/agua.mp3')}}',
                ]
            }),
            somAguaCOM: new Howl({
                src: [
                    '{{asset('audio/agua.mp3')}}',
                ]
            }),
            somBomba: new Howl({
                src: [
                    '{{asset('audio/bomba.mp3')}}',
                ]
            }),
            somBombardeio: new Howl({
                src: [
                    '{{asset('audio/bombardeio.mp3')}}',
                ]
            }),
        }

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        function desabilitarJogador(vezDoCom){
            if(!vezDoCom){
                vezDoJogador = false;
                return true;
            }
            return false;
        }

        function habilitarJogador(vezTemp){
            if(vezTemp){
                vezDoJogador = true;
            }else{
                if(!ganhou){
                    realizarJogadaCOM();
                }
            }
        }

        async function reproduzirBombardeioCompleto(nav, vezDoCom){
            let vezTemp = desabilitarJogador(vezDoCom);
            for(let k = 0; k < nav.tamanho; k++){
                sonsEspeciais.somBombardeio.play();
                await sleep(900)
            }
            habilitarJogador(vezTemp);
        }

        function enableMute() {
            if (bgaudio.muted){
                bgaudio.muted = false;
            } else {
                bgaudio.muted = true;
            }
        }

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

        function loadNavioAfundado(sourceNavio, navio, cas, nav, casas, navios){
            images[sourceNavio] = new Image();
            images[sourceNavio].onload = function(){
                navio.image(images[sourceNavio]);
                navio.setX(nav.x);
                navio.setY(nav.y);
                navio.rotation(getRotacaoNavio(cas, casas, navios));
            }
            stage.add(navioLayer);
            images[sourceNavio].src = naviosSources[sourceNavio];
        };

        function loadBordaTabuleiro(){
            images['borda_tabuleiro'] = new Image();
            images['borda_tabuleiro'].onload = function(){
                var bordaTabuleiro = new Konva.Image({
                    image: images['borda_tabuleiro'],
                    x: 5,
                    y: 17,
                    width: espacoEntreCasas*(tamanhoTabuleiro+(1.9)),
                    height: espacoEntreCasas*(tamanhoTabuleiro+(1.4)),
                    name: 'tabuleiro',
                });
                casaLayer.add(bordaTabuleiro);
            }
            images['borda_tabuleiro'].src = '{{asset('img/borda_tabuleiro.png')}}';
        };

        function atualizarTabuleiro(posicaoX){
            let tabuleiroImage = stage.find('.tabuleiro');
            for(let key in tabuleiroImage){
                let tabu = tabuleiroImage[key];
                tabu.setX(posicaoX);
            }
        };

        function initNaviosCasas(){
            for(let i = 1; i <= tamanhoTabuleiro; i++){ //criacao das casas
                for(let j = 1; j <= tamanhoTabuleiro; j++){
                    casasCOM['casa'+j+'x'+i] = {x: espacoEntreCasas*i, y: espacoEntreCasas*j, linha: j, coluna: i, ocupada: "0", navio: "", id: document.getElementById('casaCOM'+j+'x'+i).value, posicao: "-1", acertado: false,}; //cria as casas dando espaco e nome unico
                    casasPlayer['casa'+j+'x'+i] = {x: espacoEntreCasas*i+(espacoEntreCasas*(tamanhoTabuleiro+2)), y: espacoEntreCasas*j, linha: j, coluna: i, ocupada: "0", navio: "", id: document.getElementById('casaPlayer'+j+'x'+i).value, posicao: "-1", acertado: false,};
                    casasDisponiveis.push(document.getElementById('casaPlayer'+j+'x'+i).value);
                }
            };
            for(let i = 1; i <= quantidadeNavios; i++){
                naviosCOM['navio'+i] = {x: espacoEntreCasas*(tamanhoTabuleiro+1), y: (espacoEntreCasas*(i+1)), vida: parseInt(document.getElementById('tamanho_navioCOM'+i).value), tamanho: parseInt(document.getElementById('tamanho_navioCOM'+i).value), id: document.getElementById('navioCOM'+i).value};
                naviosPlayer['navioP'+i] = {x: espacoEntreCasas*(tamanhoTabuleiro+1), y: (espacoEntreCasas*(i+1)), vida: parseInt(document.getElementById('tamanho_navioPlayer'+i).value), tamanho: parseInt(document.getElementById('tamanho_navioPlayer'+i).value), id: document.getElementById('navioPlayer'+i).value};
            };
        };

        function getNavio(cas, navios){
            for(let key in navios){
                if(cas.navio == navios[key].id){
                    return navios[key];
                }
            }
        };

        function getNavioTamanho(cas, navios){
            let nav = getNavio(cas, navios);
            return nav.tamanho;
        };

        function getNavioKonva(nav, navios){
            let imagesNavios = stage.find('.navio');
            for(let key in imagesNavios){
                navio = imagesNavios[key];
                let navtemp = navios[navio.id()];
                if(navtemp != undefined){
                    if(navtemp.id == nav.id){
                        return navio;
                    }
                }
            }
        };

        function getCasaConva(cas, casas){
            let imagesCasa = stage.find('.casa');
            for(let key in imagesCasa){
                casa =  imagesCasa[key];
                let castemp = casas[casa.id()];
                if(castemp.id == cas.id){
                    return casa;
                }
            }
        };

        function getCasaPorID(id, casas){
            for(let key in casas){
                if(casas[key].id == id){
                    return casas[key];
                }
            }
        };

        function getRotacaoNavio(cas, casas, navios){
            let nav = getNavio(cas, navios);
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

        function getCasaPlot(cas, nav, angulo, casas){
            if(angulo == 0){
                for(let key in casas){
                    let casatemp = casas[key];
                    if(casatemp.navio == cas.navio && casatemp.posicao == 1){
                        return casatemp;
                    }
                }
            }else{
                for(let key in casas){
                    let casatemp = casas[key];
                    if(casatemp.navio == cas.navio && casatemp.posicao == nav.tamanho){
                        return casatemp;
                    }
                }
            }
        };

        function setNaviosPosicoes(cas, navios, casas){
            let nav = getNavio(cas, navios);
            let angulo = getRotacaoNavio(cas, casas, navios);
            let casatemp = getCasaPlot(cas, nav, angulo, casas);
            if(angulo == 0){
                nav.x = casatemp.x+5;
                nav.y = casatemp.y+5;
            }else{
                nav.x = casatemp.x+5;
                nav.y = casatemp.y+espacoDeEncaixe+15;
            }
        };

        function navioAtingido(cas, navios){
            let nav = getNavio(cas, navios);
            nav.vida -= 1;
        };

        function afundarNavio(cas, navios, casas, vezDoCom){
            let nav = getNavio(cas, navios);
            if(nav.vida == 0){
                loadNavioAfundado(getNavioSource(nav), getNavioKonva(nav, navios), cas, nav, casas, navios);
                reproduzirBombardeioCompleto(nav, vezDoCom);
            }
        };

        function initStageNavios(navios) {
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

        function initStageCasasCOM(images){
            for (let key in casasCOM) {
                (function () {
                    let imageObj = images[key];
                    let cas = casasCOM[key];

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
                        if(vezDoJogador){
                            if(!cas.acertado){
                                cas.acertado = true;
                                $.ajax({
                                    url: "{{route('atirar')}}",
                                    type: "GET",
                                    data: {
                                        casa_id:    cas.id,
                                    },
                                    statusCode: {
                                        310: function(data){
                                            vezDoJogador = false;
                                            casa.image(images['cell_board_water']);
                                            sonsEspeciais.somAgua.play();
                                            sleep(1000)
                                                .then(()=> {atualizarTabuleiro(665); })
                                                .then(()=> {realizarJogadaCOM(); })
                                        },
                                        311: function(data){
                                            cas.navio = data.responseJSON.navio_id;
                                            cas.posicao = data.responseJSON.posicao_do_navio;
                                            cas.ocupada = "1";
                                            casa.image(images['cell_board_bomb']);
                                            sonsEspeciais.somBomba.play();
                                            sleep(1500)
                                                .then(()=> {navioAtingido(cas, naviosCOM); })
                                        },
                                        312: function(data){
                                            cas.navio = data.responseJSON.navio_id;
                                            cas.posicao = data.responseJSON.posicao_do_navio;
                                            cas.ocupada = "1";
                                            casa.image(images['cell_board_bomb']);
                                            let vezTemp = desabilitarJogador(false);
                                            sonsEspeciais.somBomba.play();
                                            setNaviosPosicoes(cas, naviosCOM, casasCOM);
                                            sleep(1500)
                                                .then(()=> {navioAtingido(cas, naviosCOM); })
                                                .then(()=> {afundarNavio(cas, naviosCOM, casasCOM, false); })
                                        },
                                        313: function(data){
                                            cas.navio = data.responseJSON.navio_id;
                                            cas.posicao = data.responseJSON.posicao_do_navio;
                                            cas.ocupada = "1";
                                            casa.image(images['cell_board_bomb']);
                                            sonsEspeciais.somBomba.play();
                                            setNaviosPosicoes(cas, naviosCOM, casasCOM);
                                            ganhou = true;
                                            vezDoJogador = false;
                                            sleep(1500)
                                                .then(()=> {navioAtingido(cas, naviosCOM); })
                                                .then(()=> {afundarNavio(cas, naviosCOM, casasCOM, true); })
                                                .then(()=> {alert('Você Ganhou!'); })

                                        },
                                    },
                                });
                            }else{
                                alert("Casa já acertada");
                            }
                        }
                    });

                    casaLayer.add(casa);
                })();
            }
            stage.add(casaLayer);
        };


        function realizarJogadaCOM(){
            let IDCasa = selecionarCasaAleatoria();
            let casaJogada = getCasaPorID(IDCasa, casasPlayer);
            resultadoJogadaCOM(casaJogada, getCasaConva(casaJogada, casasPlayer));
        };

        function selecionarCasaAleatoria(){
            let selecionado = casasDisponiveis[Math.floor(Math.random()*casasDisponiveis.length)];
            casasDisponiveis.splice(casasDisponiveis.indexOf(selecionado), 1);
            return selecionado;
        };

        function resultadoJogadaCOM(cas, casa){
            cas.acertado = true;
            $.ajax({
                url: "{{route('atirar')}}",
                type: "GET",
                data: {
                    casa_id:    cas.id,
                },
                statusCode: {
                    310: function(data){
                        casa.image(images['cell_board_water']);
                        sonsEspeciais.somAguaCOM.play();
                        sleep(1000)
                            .then(()=> {atualizarTabuleiro(5); })
                            .then(()=> {vezDoJogador = true; })
                    },
                    311: function(data){
                        cas.navio = data.responseJSON.navio_id;
                        cas.posicao = data.responseJSON.posicao_do_navio;
                        cas.ocupada = "1";
                        casa.image(images['cell_board_bomb']);
                        sonsEspeciais.somBomba.play();
                        sleep(1500)
                            .then(()=> {navioAtingido(cas, naviosPlayer); })
                            .then(()=> {realizarJogadaCOM(); })
                    },
                    312: function(data){
                        cas.navio = data.responseJSON.navio_id;
                        cas.posicao = data.responseJSON.posicao_do_navio;
                        cas.ocupada = "1";
                        casa.image(images['cell_board_bomb']);
                        sonsEspeciais.somBomba.play();
                        setNaviosPosicoes(cas, naviosPlayer, casasPlayer);
                        sleep(1500)
                            .then(()=> {navioAtingido(cas, naviosPlayer); })
                            .then(()=> {afundarNavio(cas, naviosPlayer, casasPlayer, true); })
                    },
                    313: function(data){
                        cas.navio = data.responseJSON.navio_id;
                        cas.posicao = data.responseJSON.posicao_do_navio;
                        cas.ocupada = "1";
                        casa.image(images['cell_board_bomb']);
                        sonsEspeciais.somBomba.play();
                        setNaviosPosicoes(cas, naviosPlayer, casasPlayer);
                        ganhou = true;
                        vezDoJogador = false;
                        sleep(1500)
                            .then(()=> {navioAtingido(cas, naviosPlayer); })
                            .then(()=> {afundarNavio(cas, naviosPlayer, casasPlayer, true); })
                            .then(()=> {alert('Você Perdeu!'); })
                    },
                },
            })
        };

        function initStageCasasPlayer(images){
            for (let key in casasPlayer) {
                (function () {
                    let imageObj = images[key];
                    let cas = casasPlayer[key];

                    let casa = new Konva.Image({
                        x: cas.x,
                        y: cas.y,
                        image: imageObj,
                        name: 'casa',
                        id: ''+key,
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
        loadBordaTabuleiro();
        loadImages(casasSources, initStageCasasCOM);//carrega o stage pra iniciar os bagulhos
        loadImages(casasSources, initStageCasasPlayer);//carrega o stage pra iniciar os bagulhos
        initStageNavios(naviosCOM);
        initStageNavios(naviosPlayer);

    </script>
@endsection
