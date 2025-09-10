<div>

    <x-modal-general  wire:ignore.self >
        <x-slot name="tama">modal-lg</x-slot>
        <x-slot name="id_modal">modalRolesPermissions</x-slot>
        <x-slot name="titleModal">Permisos por rol</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_roles_and_permissions">
                <div class="row mt-2">
                    @foreach($listar_permisos_general as $index => $v)
                        <div class="col-lg-12">
                            <div class="accordion" id="accordionExample_{{$index}}">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne_{{$index}}" aria-expanded="false" aria-controls="collapseOne_{{$index}}">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model="check"  id="edit_check_permisos_{{ $v->id }}" value="{{ $v->id }}" >
                                                <label class="form-check-label" for="edit_check_permisos_{{ $v->id }}"> {{ $v->name }} </label>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseOne_{{$index}}" class="accordion-collapse collapse " data-bs-parent="#accordionExample_{{$index}}">
                                        <div class="">
                                            <ul class="list-group">
                                                @foreach($v->sub as $s)
                                                    <li class="list-group-item">
                                                        <input class="form-check-input  me-1"  wire:model="check" type="checkbox"  id="edit_check_permisos_{{ $s->id }}" value="{{ $s->id }}" >
                                                        <label class="form-check-label" for="edit_check_permisos_{{ $s->id }}"> {{ $s->name }} </label>
                                                        <ul class="list-group mt-2">
                                                            @foreach($s->permisos as $p)
                                                                <li class="list-group-item" style="border: none!important;">
                                                                    <input class="form-check-input  me-1"  wire:model="check" type="checkbox"  id="edit_check_permisos_{{ $p->id }}" value="{{ $p->id }}"  >
                                                                    <label class="form-check-label" for="edit_check_permisos_{{ $p->id }}"> {{ $p->name }} </label>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-lg-12 col-md-12 col-sm-12 mt-4">
                        @error('check')
                             <span class="message-error">{{ $message }}</span>
                        @enderror

                        @if (session()->has('success_permissions'))
                            <div class="alert alert-success alert-dismissible show fade">
                                {{ session('success_permissions') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session()->has('error_permissions'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_permissions') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mt-4 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-success text-white">Guardar Registros</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-general>
    <x-modal-general  wire:ignore.self >
        {{--        <x-slot name="tama">modal-lg</x-slot>--}}
        <x-slot name="id_modal">modalRoles</x-slot>
        <x-slot name="titleModal">Gestionar Rol</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_roles">
                <div class="row mt-2">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                        <label for="name" class="form-label">Nombre del rol (*)</label>
                        <x-input-general  type="text" id="name" wire:model="name"/>
                        @error('name')
                            <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
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
        <x-slot name="id_modal">modalRolesDelete</x-slot>
        <x-slot name="modalContentDelete">
            <form wire:submit.prevent="disable_roles">
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
            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modalRoles" >
                <x-slot name="tooltip">
                    Agregar Rol
                </x-slot>
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Rol
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
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Permisos</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>

                        <x-slot name="tbody">
                            @if(count($roles) > 0)
                                @php $conteoRol  = 1; @endphp
                                @foreach($roles as $ro)
                                    <tr>
                                        <td>{{$ro->numero}}</td>
                                        <td>{{$ro->name}}</td>
                                        <td>
                                            <span class="font-bold badge {{$ro->roles_status == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$ro->roles_status == 1 ? 'Habilitado ' : 'Desabilitado'}}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn bg-warning btn-sm text-white" wire:click="listar_permissions_roles('{{ base64_encode($ro->id) }}')" data-bs-toggle="modal" data-bs-target="#modalRolesPermissions">
                                                {{$ro->permisos}}
                                            </button>
                                        </td>
                                        <td>

                                            <x-btn-accion class="text-primary"  wire:click="edit_roles('{{ base64_encode($ro->id) }}')"  data-bs-toggle="modal" data-bs-target="#modalRoles" >
                                                <x-slot name="tooltip">
                                                    Editar Rol
                                                </x-slot>
                                                <x-slot name="message">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </x-slot>
                                            </x-btn-accion>


                                            @if($ro->roles_status == 1)
                                                <x-btn-accion class=" text-danger" wire:click="btn_disable('{{ base64_encode($ro->id) }}',0)" data-bs-toggle="modal" data-bs-target="#modalRolesDelete" >
                                                    <x-slot name="tooltip">
                                                        Deshabilitar Rol
                                                    </x-slot>
                                                    <x-slot name="message">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </x-slot>
                                                </x-btn-accion>
                                            @else
                                                <x-btn-accion class=" text-success" wire:click="btn_disable('{{ base64_encode($ro->id) }}',1)"  data-bs-toggle="modal" data-bs-target="#modalRolesDelete" >
                                                    <x-slot name="tooltip">
                                                        Habilitar Rol
                                                    </x-slot>
                                                    <x-slot name="message">
                                                        <i class="fa-solid fa-check"></i>
                                                    </x-slot>
                                                </x-btn-accion>
                                            @endif

                                        </td>
                                    </tr>
                                    @php $conteoRol++; @endphp
                                @endforeach
                            @else
                                <tr class="odd">
                                    <td valign="top" colspan="5" class="dataTables_empty text-center">
                                        No se han encontrado resultados.
                                    </td>
                                </tr>
                            @endif
                        </x-slot>
                    </x-table-general>
                </div>
                @if ($roles->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $roles->links(data: ['scrollTo' => false]) }}
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
    $wire.on('hideModal', () => {
        $('#modalRoles').modal('hide');
    });
    $wire.on('hideModalPermissions', () => {
        $('#modalRolesPermissions').modal('hide');
    });
    $wire.on('hideModalDelete', () => {
        $('#modalRolesDelete').modal('hide');
    });
</script>
@endscript
