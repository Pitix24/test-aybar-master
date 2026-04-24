@extends('layouts.cliente.layout-cliente')

@section('titulo', 'Avance del Proyecto')

@section('contenidoCliente')

    <div class="g_gap_pagina">
        @livewire('cliente.avance-proyecto.avance-proyecto-todo')
    </div>
@endsection