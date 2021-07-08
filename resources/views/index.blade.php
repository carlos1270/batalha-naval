@extends('layouts.app')

@section('content')

    <nav id="navbar" class="navbar navbar-dark fixed-top">
        <a class="navbar-brand mx-auto" href="http://ufape.edu.br/" target="_blank"><img src="img/ufape.png" alt="" ></a>
        <a class="btn btn-outline-light btn-lg" onclick="enableMute()" target="_blank">Som</a>
    </nav>

    <div class="video-background">
        <div class="video-wrap">
            <div id="video">
                <video id="bgivd" autoplay loop muted playsinline>
                    <source src="{{asset('video/mar.mp4')}}" type="video/mp4">
                </video>
            </div>
        </div>
    </div>

    <div class="caption text-center">
        <h2>VENÇA A BATALHA NAVAL AFUNDANDO AS EMBARCAÇÕES INIMIGAS</h2>
        <h3>O CAPITÃO ESTÁ TE ESPERANDO! VÁ!</h3>
        <a class="btn btn-outline-light btn-lg" href="{{route('jogo.new')}}">JOGAR</a>
    </div>

    <div id="git" class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1>Acesse o repositório do projeto no GitHub</h1>
            <a class="btn btn-secondary btn-sm" href="https://github.com/carlos1270/batalha-naval" target="_blank">GitHub</a>
        </div>
    </div>

@endsection
