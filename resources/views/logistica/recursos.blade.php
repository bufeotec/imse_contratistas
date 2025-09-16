@extends('layouts.intranet.template')
@section('title','Recursos')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de recursos activos registrados en el sistema." />
        @livewire('logistica.recursos')

    </div>

@endsection
