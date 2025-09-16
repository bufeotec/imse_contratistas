@extends('layouts.intranet.template')
@section('title','Registrar Documentos')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de documentos activos registrados en el sistema." />
        @livewire('controldocumentario.registrardocumentos')

    </div>

@endsection
