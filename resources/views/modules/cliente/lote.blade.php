@extends('layouts.cliente.layout-cliente')

@section('titulo', 'Lotes cliente')

@section('contenidoCliente')
    @livewire('cliente.lote.lote-todo')
@endsection