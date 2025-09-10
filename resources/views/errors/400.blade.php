
@extends('layouts.errors.template')
@section('title','400')
@section('content')
{{--    <div class="container-xxl container-p-y">--}}
{{--        <div class="misc-wrapper">--}}
{{--            <h2 class="mb-2 mx-2">Solicitud incorrecta :(</h2>--}}
{{--            <p class="mb-4 mx-2">¡Ups! 😖 La URL solicitada no se encontró en este servidor.</p>--}}
{{--            <a href="{{route('intranet')}}" class="btn btn-primary">Volver</a>--}}
{{--            <div class="mt-3">--}}
{{--                <img--}}
{{--                    src="{{asset('errors/400.svg')}}"--}}
{{--                    alt="page-misc-error-light"--}}
{{--                    width="500"--}}
{{--                    class="img-fluid"--}}
{{--                    data-app-dark-img="illustrations/page-misc-error-dark.png"--}}
{{--                    data-app-light-img="illustrations/page-misc-error-light.png"--}}
{{--                />--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


    <div class="error-page container">
        <div class="col-md-8 col-12 offset-md-2 text-center">
            <img class="img-error" src="{{asset('errors/400.svg')}}" style="width: 70%;" alt="Not Found">
            <div class="text-center">
                <h1 class="error-title mt-2">Solicitud incorrecta</h1>
                <p class="fs-5 text-gray-600">
                    La solicitud no pudo ser procesada debido a un error en la entrada. Por favor, verifica los datos enviados e intenta nuevamente.
                </p>
                <a href="{{route('intranet')}}" class="btn btn-lg btn-outline-primary mt-3 mb-4">Volver</a>
            </div>
        </div>
    </div>
    <style>
        #error {
            height: 100vh;
            background-color: #ebf3ff;
            padding-top: 4rem;
        }
    </style>
@endsection
