@extends('layouts.intranet.template')
@section('title','Menus')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de menús activos registrados en el sistema." />

        @livewire('configuracion.menus')

    </div>



@endsection
