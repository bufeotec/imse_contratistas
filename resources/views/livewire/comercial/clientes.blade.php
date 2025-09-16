<div>

    {{--    MODAL DE CREAR / EDITAR--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_cliente</x-slot>
        <x-slot name="titleModal">Gestionar cliente</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_cliente">
                <div class="row">
                    <div class="col-6 col-md-6 col-sm-12 mb-3">
                        <x-input-general type="hidden" id="id_cliente" wire:model="id_cliente" />
                        <label for="id_tipo_documento" class="form-label">Tipo de documento (*)</label>
                        <select
                            @if(!$mostrar) disabled @endif
                        id="id_tipo_documento" wire:model="id_tipo_documento" class="form-select">
                            <option value="">Seleccionar</option>
                            @foreach($listar_tipo_doc as $ltd)
                                <option value="{{$ltd->id_tipo_documento}}">{{$ltd->tipo_documento_identidad_abr}}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_documento')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-6 col-md-6 col-sm-6 mb-3">
                        <label for="cliente_numero_documento" class="form-label">Número de documento (*)</label>
                        <x-input-general type="text" id="cliente_numero_documento" wire:change="consultarDocumento" wire:model="cliente_numero_documento"/>
                        @error('cliente_numero_documento')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                        <div wire:loading wire:target="consultarDocumento">
                            Consultando información
                        </div>
                        @if($messageConsulta)
                            <span class="text-{{$messageConsulta['type']}} d-block">{{$messageConsulta['mensaje']}}</span>
                        @endif
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <label for="cliente_razon_social" class="form-label">Razón Social (*)</label>
                        <x-input-general :readonly="!$mostrar" type="text" id="cliente_razon_social" wire:model="cliente_razon_social" />
                        @error('cliente_razon_social')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <label for="cliente_nombre_comercial" class="form-label">Nombre Comercial (*)</label>
                        <x-input-general  type="text" id="cliente_nombre_comercial" wire:model="cliente_nombre_comercial" />
                        @error('cliente_nombre_comercial')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="cliente_telefono" class="form-label">Celular (*)</label>
                        <x-input-general  type="text" id="cliente_telefono" wire:model="cliente_telefono" onkeyup="validar_numeros(this.id)" />
                        @error('cliente_telefono')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="cliente_email" class="form-label">Email</label>
                        <x-input-general  type="text" id="cliente_email" wire:model="cliente_email" />
                        @error('cliente_email')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-4">
                        <label for="cliente_direccion" class="form-label">Dirección (*)</label>
                        <textarea id="cliente_direccion" name="cliente_direccion" rows="3" wire:model="cliente_direccion" class="form-control"></textarea>
                        @error('cliente_direccion')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-1">
                        <h6>Persona de contacto</h6>
                        <hr>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="cliente_persona_contacto" class="form-label">Nombre de contacto</label>
                        <x-input-general  type="text" id="cliente_persona_contacto" wire:model="cliente_persona_contacto" />
                        @error('cliente_persona_contacto')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="cliente_numero_contacto" class="form-label">Numero del contacto</label>
                        <x-input-general  type="text" id="cliente_numero_contacto" wire:model="cliente_numero_contacto" onkeyup="validar_numeros(this.id)" />
                        @error('cliente_numero_contacto')
                        <span class="message-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <label for="cliente_observacion" class="form-label">Observaciones</label>
                        <textarea id="cliente_observacion" name="cliente_observacion" class="form-control" wire:model="cliente_observacion" rows="3"></textarea>
                        @error('cliente_observacion')<span class="message-error">{{ $message }}</span>@enderror
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
    {{--    FIN MODAL DE CREAR / EDITAR--}}

    {{--    MODAL INFORMACION CLIENTE--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_informacion_cliente</x-slot>
        <x-slot name="titleModal">Información del cliente</x-slot>
        <x-slot name="tama">modal-lg</x-slot>
        <x-slot name="modalContent">
            <div class="row">
                @if($info_cliente)
                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Tipo de Documento:</strong>
                        <p>{{ $info_cliente->tipo_documento_identidad_abr }}</p>
                    </div>

                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Número de Documento:</strong>
                        <p>{{ $info_cliente->cliente_numero_documento }}</p>
                    </div>

                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Razón Social:</strong>
                        <p>{{ $info_cliente->cliente_razon_social }}</p>
                    </div>

                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Nombre Comercial:</strong>
                        <p>{{ $info_cliente->cliente_nombre_comercial }}</p>
                    </div>

                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Dirección:</strong>
                        <p>{{ $info_cliente->cliente_direccion }}</p>
                    </div>

                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Celular:</strong>
                        <p>{{ $info_cliente->cliente_telefono }}</p>
                    </div>

                    <div class="col-lg-3">
                        <strong style="color: #e10b16">Email:</strong>
                        <p>{{ $info_cliente->cliente_email ?? ' - '}}</p>
                    </div>

                    @if($info_cliente && (!empty($info_cliente->cliente_persona_contacto) || !empty($info_cliente->cliente_numero_contacto) || !empty($info_cliente->cliente_observacion)))
                        <div class="col-lg-12 mt-3">
                            <h6>Persona de contacto</h6>
                            <hr>
                        </div>

                        <div class="col-lg-4">
                            <strong style="color: #e10b16">Nombre del Contacto:</strong>
                            <p>{{ $info_cliente->cliente_persona_contacto ?? ' - ' }}</p>
                        </div>

                        <div class="col-lg-4">
                            <strong style="color: #e10b16">Número del Contacto:</strong>
                            <p>{{ $info_cliente->cliente_numero_contacto ?? ' - ' }}</p>
                        </div>

                        <div class="col-lg-4">
                            <strong style="color: #e10b16">Observaciones:</strong>
                            <p>{{ $info_cliente->cliente_observacion ?? ' - ' }}</p>
                        </div>
                    @endif

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                @else
                    <p>No hay información disponibles para mostrar.</p>
                @endif

                <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                </div>
            </div>
        </x-slot>
    </x-modal-general>
    {{--    FIN MODAL INFORMACION CLIENTE--}}

    <div class="row">
        <div class="col-lg-4 col-md-3 col-sm-12 mt-4 mb-2 ">
            <div class="d-flex align-items-center w-100">
                <input type="text" class="form-control w-50 me-4"  wire:model.live="search_cliente" placeholder="Buscar">
                <x-select-filter wire:model.live="pagination_cliente" />
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-12 mt-4 mb-2 ">
        </div>

       {{-- <div class="col-lg-2" style="margin-top: -5px;">
            <label for="id_tipo_documento_busqueda" class="form-label">Tipo de documento (*)</label>
            <select id="id_tipo_documento_busqueda" wire:model.live="id_tipo_documento_busqueda" class="form-select">
                <option value="">Seleccionar</option>
                @foreach($listar_tipo_doc_busqueda as $ltd)
                    <option value="{{$ltd->id_tipo_documento}}">{{$ltd->tipo_documento_identidad_abr}}</option>
                @endforeach
            </select>
        </div>--}}

        <div class="col-lg-2"></div>

        <div class="col-lg-3 text-end mt-4 mb-2">
            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modal_cliente">
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Cliente
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
                            <tr class="text-center">
                                <th>N°</th>
                                <th>Tipo documento</th>
                                <th>Nombre del cliente</th>
                                <th>numero documento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if(count($listar_cliente) > 0)
                                @php $conteo = 1; @endphp
                                @foreach($listar_cliente as $lc)
                                    <tr class="text-center">
                                        <td>{{$conteo}}</td>
                                        <td>{{$lc->tipo_documento_identidad_abr}}</td>
                                        <td>{{$lc->cliente_razon_social}}</td>
                                        <td>{{$lc->cliente_numero_documento}}</td>
                                        <td>
                                            <span class="font-bold badge {{$lc->cliente_estado == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$lc->cliente_estado == 1 ? 'Habilitado ' : 'Desabilitado'}}
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm bg-primary text-white ms-2" wire:click="modal_informacion_cliente('{{base64_encode($lc->id_cliente)}}')" data-bs-toggle="modal" data-bs-target="#modal_informacion_cliente"><i class="fa-solid fa-circle-info"></i></a>

                                            <a class="btn btn-sm bg-warning text-white ms-2"
                                               wire:click="editar('{{ base64_encode($lc->id_cliente) }}')"
                                               data-bs-toggle="modal" data-bs-target="#modal_cliente">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @php $conteo++; @endphp
                                @endforeach
                            @else
                                <tr class="odd">
                                    <td valign="top" colspan="11" class="dataTables_empty text-center">
                                        No se han encontrado resultados.
                                    </td>
                                </tr>
                            @endif
                        </x-slot>
                    </x-table-general>
                </div>
                @if ($listar_cliente->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $listar_cliente->links(data: ['scrollTo' => false]) }}
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
        $('#modal_cliente').modal('hide');
    });
</script>
@endscript
