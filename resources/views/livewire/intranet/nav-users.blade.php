<div class=" ms-auto">
    @php
        if ($userAuth){
            $user = \App\Models\User::find($userAuth->id_users);
            $role = $user->roles()->first();
            $roleName = "";
            if ($role) {
                $roleName = $role->name;
            }
             if (file_exists($userAuth->profile_picture)){
                $rutaImagen = $userAuth->profile_picture;
            }else{
                $rutaImagen = "assets/img/avatars/1.jpg";
            }
        }
    @endphp
    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item lh-1 me-3">
            <div class="flex-grow-1">
                <span class="fw-semibold d-block mb-1">
                    @if($userAuth)
                        {{ $userAuth->name }}
                    @endif
                </span>
                <small class="text-muted">{{$roleName }}</small>
            </div>
        </li>
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{asset($rutaImagen)}}" alt class="w-px-40 h-auto rounded-circle" />
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{asset($rutaImagen)}}" alt class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block">
                                    @if($userAuth)
                                        {{ $userAuth->name }}
                                    @endif
                                </span>
                                <small class="text-muted">{{$roleName }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{route('intranet.perfil')}}">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">Mi perfil</span>
                    </a>
                </li>
{{--                <li>--}}
{{--                    <a class="dropdown-item" href="#">--}}
{{--                        <i class="bx bx-cog me-2"></i>--}}
{{--                        <span class="align-middle">Settings</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <a class="dropdown-item" href="#">--}}
{{--                        <span class="d-flex align-items-center align-middle">--}}
{{--                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>--}}
{{--                          <span class="flex-grow-1 align-middle">Billing</span>--}}
{{--                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>--}}
{{--                        </span>--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    @livewire('auth.logout')

                </li>
            </ul>
        </li>
    </ul>
</div>
