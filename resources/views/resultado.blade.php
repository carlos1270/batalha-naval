@extends('layouts.resultado')

@section('content')
<div class="container">
  <div class="row justify-content-center">
      <div class="card text-center" style="position: relative; bottom: 550px; width: 60%;">
          <div id="card-header-app" class="card-header">
            <span>RESULTADO DA PARTIDA</span>
          </div>
          <div class="card-body">
            <div id="div-image-result" class="row justify-content-center">
                <div class="col-sm-8">
                  <img src="@if($img == 1){{asset('img/ganhamo.png')}}@else{{asset('img/perdemo.jpeg')}}@endif" alt="..." style="width: 250px;">
                </div>
            </div>
            <h5 class="card-title">{{$msg_1}}</h5>
            <p class="card-text">{{$msg_2}}</p>
            
            <a href="{{route('index')}}" class="btn btn-secondary">Início</a>
            <a id="btn-new-game" href="{{route('jogo.new')}}" class="btn btn-primary">Novo jogo</a>
          </div>
      </div>
  </div>
</div>
@endsection