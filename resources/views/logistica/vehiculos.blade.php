@extends('layouts.intranet.template')
@section('title','Vehículos')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de vehículos activos registrados en el sistema." />
        @livewire('logistica.vehiculos')

    </div>

@endsection
