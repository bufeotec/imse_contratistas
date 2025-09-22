@extends('layouts.intranet.template')
@section('title','Historial de Guías')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de todas las guías registrados en el sistema." />

        @livewire('reportes.historial-guias')

    </div>



@endsection
