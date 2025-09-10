<div>
    <form wire:submit.prevent="login" class="pt-3">
        <div class="mb-3">
            <label for="email" class="form-label">Nombre de usuario o correo electrónico</label>
            <input
                type="text"
                class="form-control @error('email') is-invalid @enderror"
                wire:model="email"
                autofocus
                id="email"
                placeholder="Nombre de usuario o correo electrónico"
            />
            @error('email')
                <span class="message-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Contraseña</label>
                <a href="{{route('password.request')}}">
                    <small>¿Has olvidado tu contraseña?</small>
                </a>
            </div>
            <div class="input-group input-group-merge">
                <input
                    type="password"
                    id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    wire:model="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            @error('password')
                <span class="message-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" wire:model="remember" type="checkbox" id="remember-me" />
                <label class="form-check-label" for="remember-me"> Recordar sesión </label>
            </div>
        </div>
        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Iniciar Sesión</button>
        </div>
    </form>

    <style>
        .border_ra{
            border-radius: 15px!important;
        }
    </style>
</div>

@assets
    <script src="{{asset('js/domain.js')}}"></script>
@endassets
@script
    <script>
        $wire.on('redirectAfterSuccess', function (url) {
            setTimeout(function() {
                window.location.href = url;
            }, 2000);
        });
    </script>
@endscript
