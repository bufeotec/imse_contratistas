@extends('layouts.intranet.template')
@section('title','Despachos de Transporte')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Módulo de Gestión de Despachos del Sistema." />

        @livewire('despachos.gestionar-despachos')

    </div>



@endsection
