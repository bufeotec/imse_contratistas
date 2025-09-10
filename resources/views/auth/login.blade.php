@extends('layouts.auth.template')
@section('title','Iniciar Sesión')
@section('content')


    <div class="app-brand justify-content-center">
        <a href="#" class="app-brand-link gap-2">
            <img src="{{asset('assets/img/icons/logo.png')}}" class="w-50 m-auto" alt="">
        </a>
    </div>
    <!-- /Logo -->
    <h4 class="mb-2">Bienvenido ! 👋</h4>
    <p class="mb-4">Inicia sesión en tu cuenta para comenzar.</p>
    @livewire('auth.login')
{{--    <p class="text-center">--}}
{{--        <span>New on our platform?</span>--}}
{{--        <a href="auth-register-basic.html">--}}
{{--            <span>Create an account</span>--}}
{{--        </a>--}}
{{--    </p>--}}
@endsection
