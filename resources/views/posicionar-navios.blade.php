@extends('layouts.app')

@section('content')
    <h2>Escolha a posição dos seus navios</h2>
    <input type="hidden" name="jogo_id" value="{{$jogo->id}}">
@endsection