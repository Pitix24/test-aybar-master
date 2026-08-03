@extends('layouts.cliente.layout-cliente')

@section('titulo', 'Documentos de Proyecto')

@section('contenidoCliente')

<div class="g_gap_pagina">
    @livewire('cliente.documento.documento-proyecto', ['proyecto_id' => $proyecto_id])
</div>
@endsection
