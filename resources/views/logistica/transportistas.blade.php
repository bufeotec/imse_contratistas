@extends('layouts.intranet.template')
@section('title','Transportistas')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de transportistas activos registrados en el sistema." />
        @livewire('logistica.transportistas')

    </div>

@endsection
