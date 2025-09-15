@extends('layouts.intranet.template')
@section('title','Clientes')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de clientes activos registrados en el sistema." />

        @livewire('comercial.clientes')

    </div>



@endsection
