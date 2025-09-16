<div>

    {{--    MODAL REGISTRO TRANSPORTISTAS--}}
    <x-modal-general  wire:ignore.self >
        <x-slot name="id_modal">modal_transportistas</x-slot>
        <x-slot name="tama">modal-lg</x-slot>
        <x-slot name="titleModal">Gestionar Transportistas</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_transportista">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <small class="text-primary">Información del Transportista</small>
                        <hr class="mb-0">
                    </div>

                    <div class="col-lg-6 col-md-4 col-sm-12 mb-3">
                        <label for="transportista_ruc" class="form-label">RUC (*)</label>
                        <x-input-general  type="text" id="transportista_ruc" wire:model="transportista_ruc" wire:change="consultDocument"/>
                        <div wire:loading wire:target="consultDocument">
                            Consultando información
                        </div>
                        @if($messageConsulta)<span class="text-{{$messageConsulta['type']}} d-block">{{$messageConsulta['mensaje']}}</span>@endif

                        @error('transportista_ruc')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-8 col-sm-12 mb-3">
                        <label for="transportista_razon_social" class="form-label">Razón social (*)</label>
                        <x-input-general  type="text" id="transportista_razon_social" wire:model="transportista_razon_social"/>
                        @error('transportista_razon_social')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="transportista_nom_comercial" class="form-label">Nombre comercial (*)</label>
                        <x-input-general  type="text" id="transportista_nom_comercial" wire:model="transportista_nom_comercial"/>
                        @error('transportista_nom_comercial')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="transportista_direccion" class="form-label">Dirección (*)</label>
                        <x-input-general  type="text" id="transportista_direccion" wire:model="transportista_direccion"/>
                        @error('transportista_direccion')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="transportista_telefono" class="form-label">Teléfono</label>
                        <x-input-general  type="text" id="transportista_telefono" wire:model="transportista_telefono" onkeyup="validar_numeros(this.id)"/>
                        @error('transportista_telefono')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="transportista_gmail" class="form-label">Correo</label>
                        <x-input-general  type="text" id="transportista_gmail" wire:model="transportista_gmail"/>
                        @error('transportista_gmail')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @if (session()->has('error_modal'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_modal') }}
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
    {{--    FIN MODAL REGISTRO TRANSPORTISTAS--}}

    {{--    MODAL DELETE--}}
    <x-modal-delete wire:ignore.self>
        <x-slot name="id_modal">modal_delete_transportistas</x-slot>
        <x-slot name="modalContentDelete">
            <form wire:submit.prevent="disable_transportistas">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h2 class="deleteTitle">{{$message_delete_transportista}}</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @error('id_transportista') <span class="message-error">{{ $message }}</span> @enderror

                        @error('transportista_estado') <span class="message-error">{{ $message }}</span> @enderror

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
    {{--    FIN MODAL DELETE--}}

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <input type="text" class="form-control w-50 me-4"  wire:model.live="search_transportistas" placeholder="Buscar">
            <x-select-filter wire:model.live="pagination_transportistas" />
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 text-end">
            <x-btn-export wire:click="clear_form_transportistas" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modal_transportistas" >
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Transportista
            </x-btn-export>
        </div>
    </div>

    <x-card-general-view>
        <x-slot name="content">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <x-table-general>
                        <x-slot name="thead">
                            <tr>
                                <th>N°</th>
                                <th>RUC</th>
                                <th>Razón Social</th>
                                <th>Nombre Comercial</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if(count($listar_transportista) > 0)
                                @php $conteo = 1; @endphp
                                @foreach($listar_transportista as $lt)
                                    <tr>
                                        <td>{{$conteo}}</td>
                                        <td>{{$lt->transportista_ruc}}</td>
                                        <td>{{$lt->transportista_razon_social}}</td>
                                        <td>{{$lt->transportista_nom_comercial}}</td>
                                        <td>{{$lt->transportista_direccion}}</td>
                                        <td>{{$lt->transportista_telefono ?? '-'}}</td>
                                        <td>{{$lt->transportista_gmail ?? '-'}}</td>
                                        <td>
                                            <span class="font-bold badge {{$lt->transportista_estado == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$lt->transportista_estado == 1 ? 'Habilitado ' : 'Desabilitado'}}
                                            </span>
                                        </td>
                                        <td>
                                            <x-btn-accion class=" text-primary"  wire:click="edit_data('{{ base64_encode($lt->id_transportista) }}')" data-bs-toggle="modal" data-bs-target="#modal_transportistas">
                                                <x-slot name="message">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </x-slot>
                                            </x-btn-accion>

                                            @if($lt->transportista_estado == 1)
                                                <x-btn-accion class=" text-danger" wire:click="btn_disable('{{ base64_encode($lt->id_transportista) }}',0)" data-bs-toggle="modal" data-bs-target="#modal_delete_transportistas">
                                                    <x-slot name="message">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </x-slot>
                                                </x-btn-accion>
                                            @else
                                                <x-btn-accion class=" text-success" wire:click="btn_disable('{{ base64_encode($lt->id_transportista) }}',1)" data-bs-toggle="modal" data-bs-target="#modal_delete_transportistas">
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
                @if ($listar_transportista->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $listar_transportista->links(data: ['scrollTo' => false]) }}
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
    $wire.on('hide_modal_transportistas', () => {
        $('#modal_transportistas').modal('hide');
    });
    $wire.on('hide_modal_delete_transportistas', () => {
        $('#modal_delete_transportistas').modal('hide');
    });
</script>
@endscript
