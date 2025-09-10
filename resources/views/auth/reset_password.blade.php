@extends('layouts.auth.template')
@section('title','Restablecer la contraseña')
@section('content')

    <div class="app-brand justify-content-center">
        <a href="#" class="app-brand-link gap-2">
            <img src="{{asset('assets/img/icons/logo.png')}}" class="w-50 m-auto" alt="">
        </a>
    </div>
    <h4 class="mb-2 text-center">Restablecer la contraseña</h4>
    <p class="mb-4 text-center">
        Para {{ $request->email }}
    </p>
    @livewire('auth.reset-password',['token'=>$request->route('token'),'email'=>$request->email])

@endsection
