<div>

{{--    MODAL REGISTRO RECURSO--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_recurso</x-slot>
        <x-slot name="tama">modal-lg</x-slot>
        <x-slot name="titleModal">Gestionar Recursos</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_recurso">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="recurso_nombre" class="form-label">Nombre <b class="text-danger">(*)</b></label>
                        <x-input-general type="text" id="recurso_nombre" wire:model="recurso_nombre" />
                        @error('recurso_nombre')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="id_tipo_recurso" class="form-label">Tipo de Recurso <b class="text-danger">(*)</b></label>
                        <select class="form-select" name="id_tipo_recurso" id="id_tipo_recurso" wire:model="id_tipo_recurso" >
                            <option value="">Seleccionar...</option>
                            @foreach($listar_tipos_recursos as $ltr)
                                <option value="{{$ltr->id_tipo_recurso}}">{{$ltr->tipo_recurso_concepto}}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_recurso')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="id_medida" class="form-label">Medida <b class="text-danger">(*)</b></label>
                        <select class="form-select" name="id_medida" id="id_medida" wire:model="id_medida" >
                            <option value="">Seleccionar...</option>
                            @foreach($listar_medidas as $ltr)
                                <option value="{{$ltr->id_medida}}">{{$ltr->medida_nombre}}</option>
                            @endforeach
                        </select>
                        @error('id_medida')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="recurso_cantidad" class="form-label">Cantidad <b class="text-danger">(*)</b></label>
                        <x-input-general  type="text" id="recurso_cantidad" wire:model="recurso_cantidad" onkeyup="validar_numeros(this.id)"/>
                        @error('recurso_cantidad')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="recurso_estado" class="form-label">Estado <b class="text-danger">(*)</b></label>
                        <select class="form-control" id="recurso_estado" wire:model="recurso_estado">
                            <option value="">Seleccionar...</option>
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                            <option value="3">Mantenimiento</option>
                        </select>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @if (session()->has('error_modal_agregar'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_modal_agregar') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-success text-white">Guardar Registro</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-general>
{{--    FIN MODAL REGISTRO RECURSO--}}

    {{--    MODAL DELETE RECURSO--}}
    <x-modal-delete wire:ignore.self>
        <x-slot name="id_modal">modal_delete_recurso</x-slot>
        <x-slot name="modalContentDelete">
            <form wire:submit.prevent="disable_recurso">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h2 class="deleteTitle">{{$message_delete_recurso}}</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @error('id_recurso') <span class="message-error">{{ $message }}</span> @enderror

                        @error('recurso_estado') <span class="message-error">{{ $message }}</span> @enderror

                        @if (session()->has('error_delete'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_delete') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-center">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-danger btnDelete">No</button>
                        <button type="submit" class="btn btn-primary text-white btnDelete">SI</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-delete>
    {{--    FIN MODAL DELETE RECURSO--}}

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12  mb-2 ">
            <div class="d-flex align-items-center w-100">
                <input type="text" class="form-control w-50 me-4"  wire:model.live="search_recurso" placeholder="Buscar">
                <x-select-filter wire:model.live="pagination_recurso" />
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 text-end mb-2">
            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modal_recurso" >
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Recurso
            </x-btn-export>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible show fade mt-2">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible show fade mt-2">
            {{ session('error') }}
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
                                <th>Tipo de recurso</th>
                                <th>Medida</th>
                                <th>Cantidad</th>
                                <th>Estado Movimiento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if(count($listar_recursos) > 0)
                                @php $conteo = 1; @endphp
                                @foreach($listar_recursos as $lr)
                                    <tr>
                                        <td>{{$conteo}}</td>
                                        <td>{{$lr->recurso_nombre}}</td>
                                        <td>{{$lr->tipo_recurso_concepto}}</td>
                                        <td>{{$lr->medida_nombre}}</td>
                                        <td>{{$lr->recurso_cantidad}}</td>
                                        <td>

                                        </td>
                                        <td>
                                            @php
                                            $estado = "";
                                            if ($lr->recurso_estado == 1){
                                                $estado = "Activo";
                                            } elseif ($lr->recurso_estado == 2){
                                                $estado = "Inactivo";
                                            } elseif ($lr->recurso_estado == 3){
                                                $estado = "Mantenimiento";
                                            } else {
                                                $estado = "-";
                                            }
                                            @endphp
                                            {{$estado}}
                                        </td>
                                        <td>
                                            <x-btn-accion class=" text-primary"  wire:click="edit_data('{{ base64_encode($lr->id_recurso) }}')" data-bs-toggle="modal" data-bs-target="#modal_recurso">
                                                <x-slot name="message">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </x-slot>
                                            </x-btn-accion>

{{--                                            @if($lr->recurso_estado == 1)--}}
{{--                                                <x-btn-accion class=" text-danger" wire:click="btn_disable('{{ base64_encode($lr->id_recurso) }}',0)" data-bs-toggle="modal" data-bs-target="#modal_delete_recurso">--}}
{{--                                                    <x-slot name="message">--}}
{{--                                                        <i class="fa-solid fa-ban"></i>--}}
{{--                                                    </x-slot>--}}
{{--                                                </x-btn-accion>--}}
{{--                                            @else--}}
{{--                                                <x-btn-accion class=" text-success" wire:click="btn_disable('{{ base64_encode($lr->id_recurso) }}',1)" data-bs-toggle="modal" data-bs-target="#modal_delete_recurso">--}}
{{--                                                    <x-slot name="message">--}}
{{--                                                        <i class="fa-solid fa-check"></i>--}}
{{--                                                    </x-slot>--}}
{{--                                                </x-btn-accion>--}}
{{--                                            @endif--}}
                                        </td>
                                    </tr>
                                    @php $conteo++; @endphp
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
                @if ($listar_recursos->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $listar_recursos->links(data: ['scrollTo' => false]) }}
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
    $wire.on('hide_modal_recurso', () => {
        $('#modal_recurso').modal('hide');
    });

    $wire.on('hide_modal_delete_recurso', () => {
        $('#modal_delete_recurso').modal('hide');
    });
</script>
@endscript
