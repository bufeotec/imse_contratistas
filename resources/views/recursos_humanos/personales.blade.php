@extends('layouts.intranet.template')
@section('title','Personales')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de personas activos registrados en el sistema." />

        @livewire('recursoshumanos.personales')

    </div>



@endsection
