@extends('layouts.cliente.layout-cliente')

@section('titulo', 'Inicio Cliente')

@section('contenidoCliente')

    <div class="g_gap_pagina">
        @livewire('cliente.perfil.perfil-ver')
        @livewire('cliente.perfil.direccion-editar')
        @livewire('cliente.perfil.cuenta-editar')
    </div>
@endsection