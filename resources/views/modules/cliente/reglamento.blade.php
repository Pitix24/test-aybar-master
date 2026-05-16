@extends('layouts.cliente.layout-cliente')

@section('titulo', 'Reglamentos')

@section('contenidoCliente')

<div class="g_gap_pagina">
    @livewire('cliente.reglamento.reglamento-todo')
</div>
@endsection