<div>

    @php
        $general = new \App\Models\General();
    @endphp

{{--    MODAL AGREGAR / EDITAR VEHÍCULO--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_vehiculo</x-slot>
        <x-slot name="tama">modal-lg</x-slot>
        <x-slot name="titleModal">Gestionar Vehículos</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_vehiculo">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <small class="text-primary">Información del Vehículo</small>
                        <hr class="mb-0">
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="id_transportista" class="form-label">Transportista (*)</label>
                        <select class="form-select" name="id_transportista" id="id_transportista" wire:model="id_transportista" >
                            <option value="">Seleccionar...</option>
                            @foreach($listar_transportista as $lpv)
                                <option value="{{$lpv->id_transportista}}">{{$lpv->transportista_razon_social}}</option>
                            @endforeach
                        </select>
                        @error('id_transportista')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="vehiculo_placa" class="form-label">Placa (*)</label>
                        <x-input-general  type="text" id="vehiculo_placa" wire:model="vehiculo_placa"/>
                        @error('vehiculo_placa')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="vehiculo_capacidad_peso" class="form-label">Capacidad de peso (*) (en kg)</label>
                        <x-input-general  type="text" id="vehiculo_capacidad_peso" wire:model="vehiculo_capacidad_peso" onkeyup="validar_numeros(this.id)"/>
                        @error('vehiculo_capacidad_peso')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="vehiculo_ancho" class="form-label">Ancho (*) (Medida en cm)</label>
                        <x-input-general type="text" id="vehiculo_ancho" wire:model="vehiculo_ancho" wire:input="calcularVolumen" onkeyup="validar_numeros(this.id)" />
                        @error('vehiculo_ancho')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="vehiculo_largo" class="form-label">Largo (*) (Medida en cm)</label>
                        <x-input-general type="text" id="vehiculo_largo" wire:model="vehiculo_largo" wire:input="calcularVolumen" onkeyup="validar_numeros(this.id)" />
                        @error('vehiculo_largo')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="vehiculo_alto" class="form-label">Alto (*) (Medida en cm)</label>
                        <x-input-general type="text" id="vehiculo_alto" wire:model="vehiculo_alto" wire:input="calcularVolumen" onkeyup="validar_numeros(this.id)" />
                        @error('vehiculo_alto')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @if (session()->has('error_modal_agregar'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_modal_agregar') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    @if($vehiculo_capacidad_volumen > 0)
                        @php
                            $capacidadVolumen = "0";
                            if ($vehiculo_capacidad_volumen){
                                $capacidadVolumen = $general->formatoDecimal($vehiculo_capacidad_volumen);
                            }
                        @endphp
                        <div class="col-lg-12 col-md-4 col-sm-12 mb-3">
                            <small class="d-flex justify-content-end mt-4">Capacidad de Volumen</small>
                            <div class="d-flex justify-content-end align-items-center">
                                <h3 class="numero_vehiculo">
                                    {{ $capacidadVolumen}} <span class="span_vehiculo">(cm³)</span>
                                </h3>
                            </div>
                            @error('vehiculo_capacidad_volumen')<span class="message-error">{{ $message }}</span>@enderror
                        </div>
                    @endif

                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-success text-white">Guardar Registro</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-general>
{{--    FIN MODAL AGREGAR / EDITAR VEHÍCULO--}}

{{--    MODAL HABILITAR / DESHABILITAR VEHÍCULO--}}
    <x-modal-delete  wire:ignore.self >
        <x-slot name="id_modal">modal_delete_vehiculo</x-slot>
        <x-slot name="modalContentDelete">
            <form wire:submit.prevent="disable_vehiculo">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h2 class="deleteTitle">{{$message_delete_vehiculo}}</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @error('id_vehiculo') <span class="message-error">{{ $message }}</span> @enderror

                        @error('vehiculo_estado') <span class="message-error">{{ $message }}</span> @enderror

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
{{--    FIN MODAL HABILITAR / DESHABILITAR VEHÍCULO--}}

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12  mb-2 ">
            <div class="d-flex align-items-center w-100">
                <input type="text" class="form-control w-50 me-4"  wire:model.live="search_vehiculo" placeholder="Buscar">
                <x-select-filter wire:model.live="pagination_vehiculo" />
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 text-end mb-2">
            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modal_vehiculo" >
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Vehículo
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
                                <th>Transportista</th>
                                <th>Placa</th>
                                <th>Capacidad Peso</th>
                                <th>Ancho</th>
                                <th>Largo</th>
                                <th>Alto</th>
                                <th>Capacidad Volumen</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if(count($listar_vehiculos) > 0)
                                @php $conteo = 1; @endphp
                                @foreach($listar_vehiculos as $lv)
                                    <tr>
                                        <td>{{$conteo}}</td>
                                        <td>{{$lv->transportista_razon_social ?? '-'}}</td>
                                        <td>{{$lv->vehiculo_placa}}</td>
                                        <td>{{$lv->vehiculo_capacidad_peso}}</td>
                                        <td>{{$lv->vehiculo_ancho}}</td>
                                        <td>{{$lv->vehiculo_largo}}</td>
                                        <td>{{$lv->vehiculo_alto}}</td>
                                        <td>{{$lv->vehiculo_capacidad_volumen}}</td>
                                        <td>
                                            <span class="font-bold badge {{$lv->vehiculo_estado == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$lv->vehiculo_estado == 1 ? 'Habilitado ' : 'Desabilitado'}}
                                            </span>
                                        </td>
                                        <td>
                                            <x-btn-accion class=" text-primary"  wire:click="edit_data('{{ base64_encode($lv->id_vehiculo) }}')" data-bs-toggle="modal" data-bs-target="#modal_vehiculo">
                                                <x-slot name="message">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </x-slot>
                                            </x-btn-accion>

                                            @if($lv->vehiculo_estado == 1)
                                                <x-btn-accion class=" text-danger" wire:click="btn_disable('{{ base64_encode($lv->id_vehiculo) }}',0)" data-bs-toggle="modal" data-bs-target="#modal_delete_vehiculo">
                                                    <x-slot name="message">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </x-slot>
                                                </x-btn-accion>
                                            @else
                                                <x-btn-accion class=" text-success" wire:click="btn_disable('{{ base64_encode($lv->id_vehiculo) }}',1)" data-bs-toggle="modal" data-bs-target="#modal_delete_vehiculo">
                                                    <x-slot name="message">
                                                        <i class="fa-solid fa-check"></i>
                                                    </x-slot>
                                                </x-btn-accion>
                                            @endif
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
                @if ($listar_vehiculos->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $listar_vehiculos->links(data: ['scrollTo' => false]) }}
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
    $wire.on('hide_modal_vehiculo', () => {
        $('#modal_vehiculo').modal('hide');
    });

    $wire.on('hide_modal_delete_vehiculo', () => {
        $('#modal_delete_vehiculo').modal('hide');
    });
</script>
@endscript
