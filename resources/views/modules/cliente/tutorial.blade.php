@extends('layouts.cliente.layout-cliente')

@section('titulo', 'Tutoriales')

@section('contenidoCliente')

    <div class="g_gap_pagina">
        @livewire('cliente.tutorial.tutorial-todo')
    </div>
@endsection