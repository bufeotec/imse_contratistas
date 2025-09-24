@extends('layouts.intranet.template')
@section('title','Sincerar Documentos')
@section('content')


    <div class="page-heading">
        <x-navegation-view text="Lista de documentos sincerados registrados en el sistema." />
        @livewire('controldocumentario.sincerar-documentos')

    </div>

@endsection
