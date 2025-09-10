@extends('layouts.auth.template')
@section('title','Has olvidado tu contraseña')
@section('content')

    <!-- Logo -->
    <div class="app-brand justify-content-center">
        <a href="#" class="app-brand-link gap-2">
            <img src="{{asset('assets/img/icons/logo.png')}}" class="w-50 m-auto" alt="">
        </a>
    </div>
    <!-- /Logo -->
    <h4 class="mb-2 text-center">¿Has olvidado tu contraseña? 🔒</h4>
    <p class="mb-4 text-center">
        Ingresa tu correo electrónico y te enviaremos
        instrucciones para restablecer tu contraseña
    </p>
    @livewire('auth.forgot-password')
    <div class="text-center mt-4">
        <a href="{{route('login')}}" class="d-flex align-items-center justify-content-center">
            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
            Volver al inicio de sesión
        </a>
    </div>
@endsection
