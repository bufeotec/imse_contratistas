<div>

    <x-modal-general  wire:ignore.self >
        <x-slot name="id_modal">modalUsuarios</x-slot>
        <x-slot name="titleModal">Gestionar Usuarios</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_users">
                <div class="row ">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-1">
                        <small class="text-primary" style="font-size: 11pt">Datos personales</small>
                        <hr>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row align-items-center">
                            <div class="col-lg-4 col-md-4 col-sm-12 ">
                                <div class="d-flex align-items-center justify-content-center">
                                    <label for="profile_picture" style="cursor: pointer!important;">
                                        <div style="width: 108px;" wire:ignore>
                                            <img src="" id="previeImageUsers" class="" style="width: 120px;height: 120px; border-radius: 50%;margin-top: 10%;" alt="">
                                            <div  class="iconsPreviewImage">
                                                <i class="fa-solid fa-camera "></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <input type="file" class="d-none" id="profile_picture" name="profile_picture" onchange="previewImage(this,'previeImageUsers')" wire:model="profile_picture" wire:change="cambiarImg" >
                                <div wire:loading wire:target="cambiarImg" class="w-100" >
                                    <div class="d-flex justify-content-center">
                                        <div class="loader"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-12 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                                        <label for="name" class="form-label">Nombre (*)</label>
                                        <x-input-general   type="text" id="name" wire:model="name" />
                                        @error('name')
                                        <span class="message-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                                        <label for="last_name" class="form-label">Apellido (*)</label>
                                        <x-input-general  type="text" id="last_name" wire:model="last_name"/>
                                        @error('last_name')
                                        <span class="message-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-1">
                        <small class="text-primary" style="font-size: 11pt">Datos del usuario</small>
                        <hr>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                        <label for="username" class="form-label">Nombre de Usuario (*)</label>
                        <x-input-general   type="text" id="username" wire:model="username"/>
                        @error('username')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                        <label for="email" class="form-label">Correo Electronico (*)</label>
                        <x-input-general  type="email" id="email" wire:model="email"/>
                        @error('email')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                        <label for="id_rol" class="form-label">Rol (*)</label>
                        <select id="id_rol" class=" form-select" wire:model="id_rol">
                            <option value="">Seleccionar</option>
                            @foreach($roles as $re)
                                <option value="{{$re->id}}">{{$re->name}}</option>
                            @endforeach
                        </select>
                        @error('id_rol')
                            <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>
{{--                    @if(empty($id_users))--}}
                        <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                            <label for="password" class="form-label">{{!empty($id_users) ? 'Actualizar Contraseña': 'Contraseña'}}</label>
                            <x-input-general type="password" id="password"   wire:model="password" />
                            @error('password')
                            <span class="message-error">{{ $message }}</span>
                            @enderror
                        </div>
{{--                    @endif--}}
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @error('profile_picture')
                            <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-success text-white">Guardar Registros</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-general>

    <x-modal-delete  wire:ignore.self >
        <x-slot name="id_modal">modalDeleteUsers</x-slot>
        <x-slot name="modalContentDelete">
            <form wire:submit.prevent="disable_users">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h2 class="deleteTitle">{{$messageDelete}}</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @error('id_menu') <span class="message-error">{{ $message }}</span> @enderror

                        @error('statusMenu') <span class="message-error">{{ $message }}</span> @enderror

                        @if (session()->has('error_delete'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_delete') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-center">
                        <button type="submit" class="btn btn-primary text-white btnDelete">SI</button>
                        <button type="button" data-bs-dismiss="modal" class="btn btn-danger btnDelete">No</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-delete>


    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <input type="text" class="form-control w-50 me-4"  wire:model.live="search" placeholder="Buscar">
            <x-select-filter wire:model.live="pagination" />
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 text-end mb-2">


            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modalUsuarios" >
                <x-slot name="tooltip">
                    Agregar Usuario
                </x-slot>
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Usuario
            </x-btn-export>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible show fade mt-2">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-card-general-view>
        <x-slot name="content">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <x-table-general>
                        <x-slot name="thead">
                            <tr>
                                <th>Nombre completo</th>
                                <th>Nombre de usuario</th>
                                <th>Correo electrónico</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>

                        <x-slot name="tbody">
                            @if(count($usuarios) > 0)
                                @php $conteoUsuarios = 1; @endphp
                                @foreach($usuarios as $index => $me)
                                    <tr>
{{--                                        <td>{{$conteoUsuarios}}</td>--}}
                                        <td>
                                            @if(file_exists($me->profile_picture))
                                                <img src="{{asset($me->profile_picture)}}" style="width: 40px;" class="rounded-circle" alt="">
                                            @else
                                                <img src="{{asset('assets/images/faces/1.jpg')}}" style="width: 40px;" class="rounded-circle" alt="">
                                            @endif
                                            <span class="ms-2">{{$me->name}} {{$me->last_name}}</span>
                                        </td>
                                        <td>{{$me->username}}</td>
                                        <td>{{$me->email}}</td>
                                        <td>
                                            @php
                                                $rol = \Illuminate\Support\Facades\DB::table('model_has_roles as  mr')
                                                ->join('roles as r','r.id','=','mr.role_id')->where('mr.model_id','=',$me->id_users)->first();
                                            @endphp
                                            <span class="font-bold text-warning">
                                                {{$rol->name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-bold badge {{$me->users_status == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$me->users_status == 1 ? 'Habilitado ' : 'Deshabilitado'}}
                                            </span>
                                        </td>

                                        <td>
                                            <x-btn-accion class="text-primary"  wire:click="edit_users('{{ base64_encode($me->id_users) }}')" data-bs-toggle="modal" data-bs-target="#modalUsuarios"  >
                                                <x-slot name="tooltip">
                                                    Editar Usuario
                                                </x-slot>
                                                <x-slot name="message">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </x-slot>
                                            </x-btn-accion>

                                            @if($me->id_users != 1)
                                                @if($me->users_status == 1)
                                                    <x-btn-accion class=" text-danger" wire:click="btn_disable('{{ base64_encode($me->id_users) }}',0)" data-bs-toggle="modal" data-bs-target="#modalDeleteUsers" >
                                                        <x-slot name="tooltip">
                                                            Deshabilitar Usuario
                                                        </x-slot>
                                                        <x-slot name="message">
                                                            <i class="fa-solid fa-ban"></i>
                                                        </x-slot>
                                                    </x-btn-accion>
                                                @else
                                                    <x-btn-accion class=" text-success" wire:click="btn_disable('{{ base64_encode($me->id_users) }}',1)" data-bs-toggle="modal" data-bs-target="#modalDeleteUsers" >
                                                        <x-slot name="tooltip">
                                                            Habilitar Usuario
                                                        </x-slot>
                                                        <x-slot name="message">
                                                            <i class="fa-solid fa-check"></i>
                                                        </x-slot>
                                                    </x-btn-accion>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @php $conteoUsuarios++; @endphp
                                @endforeach
                            @else
                                <tr class="odd">
                                    <td valign="top" colspan="9" class="dataTables_empty text-center">
                                        No se han encontrado resultados.
                                    </td>
                                </tr>
                            @endif
                        </x-slot>
                    </x-table-general>
                </div>
                @if ($usuarios->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $usuarios->links(data: ['scrollTo' => false]) }}
                            </x-slot>
                        </x-pagination>
                    </div>
                @endif
            </div>
        </x-slot>
    </x-card-general-view>
</div>


@script

<script>

    $wire.on('updateUserImagePreview',function(event) {
        const image = document.getElementById('previeImageUsers');
        if (image) {
            console.log(event[0].image);
            image.src = event[0].image;
        }
    });

    $wire.on('hideModal', () => {
        $('#modalUsuarios').modal('hide');
    });
    $wire.on('hideModalDelete', () => {
        $('#modalDeleteUsers').modal('hide');
    });
</script>
@endscript
