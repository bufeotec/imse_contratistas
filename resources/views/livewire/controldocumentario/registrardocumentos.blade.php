<div>

{{--    MODAL REGISTRAR DOCUMENTO--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_registrar_documento</x-slot>
        <x-slot name="tama">modal-xl</x-slot>
        <x-slot name="titleModal">Gestionar Documento</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_documento">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        @if (session()->has('error_modal_agregar'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_modal_agregar') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                <small class="text-primary">Información del Cliente</small>
                                <hr class="mb-0">
                            </div>

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

                            <div class="col-12 col-md-12 col-sm-12 mb-3">
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

                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                <small class="text-primary">Información de la guia</small>
                                <hr class="mb-0">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_serie" class="form-label">Serie (*)</label>
                                <x-input-general type="text" id="guia_serie" wire:model="guia_serie"/>
                                @error('guia_serie')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_correlativo" class="form-label">Correlativo (*)</label>
                                <x-input-general type="text" id="guia_correlativo" wire:model="guia_correlativo" />
                                @error('guia_correlativo')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_fecha_emision" class="form-label">Fecha de Emisión (*)</label>
                                <x-input-general type="date" id="guia_fecha_emision" wire:model="guia_fecha_emision" />
                                @error('guia_fecha_emision')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_trabajo_realizar" class="form-label">Trabajo a realizar (*)</label>
                                <textarea class="form-control" rows="2" id="guia_trabajo_realizar" wire:model="guia_trabajo_realizar"></textarea>
                                @error('guia_trabajo_realizar')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_modalidad_transporte" class="form-label">Modalidad de Transporte (*)</label>
                                <select class="form-control" id="guia_modalidad_transporte" wire:model="guia_modalidad_transporte">
                                    <option value="">Seleccionar...</option>
                                    <option value="1">Publico</option>
                                    <option value="2">Privado</option>
                                </select>
                                @error('guia_modalidad_transporte')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_documento" class="form-label">Documento (*)</label>
                                <x-input-general type="file" id="guia_documento" wire:model="guia_documento"/>
                                @error('guia_documento')<span class="message-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-success text-white">Guardar Registro</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-general>
{{--    FIN MODAL REGISTRAR DOCUMENTO--}}

{{--    MODAL REGISTRAR PERSONAL / RECURSO--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_registrar_personal_recurso</x-slot>
        <x-slot name="tama">modal-xl</x-slot>
        <x-slot name="titleModal">Gestionar Personal / Recurso</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_personal_recurso">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        @if (session()->has('error_modal_personal_recurso'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                {{ session('error_modal_personal_recurso') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <!-- PERSONALES -->
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                        <small class="text-primary">Información Personal</small>
                                        <hr class="mb-0">
                                    </div>

                                    <div class="col-lg-9 col-md-9 col-sm-12 mb-3">
                                        <label class="form-label">Personal</label>
                                        <select class="form-control" wire:model="personal_seleccionado">
                                            <option value="">Seleccionar...</option>
                                            @foreach($listar_personales_activos as $lpv)
                                                <option value="{{$lpv->id_personal}}">{{$lpv->personal_nombre}} {{$lpv->personal_apellido}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-12 mb-3 mt-4">
                                        <a class="btn btn-sm bg-info text-white" wire:click="agregar_personal">
                                            <i class="fa-solid fa-plus"></i>
                                        </a>
                                    </div>

                                    {{-- Mostrar mensajes de éxito o error --}}
                                    @if (session()->has('success_agregar_personal'))
                                        <div class="alert alert-success alert-dismissible show fade">
                                            {{ session('success_agregar_personal') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    @if (session()->has('error_eliminar_personal'))
                                        <div class="alert alert-danger alert-dismissible show fade">
                                            {{ session('error_eliminar_personal') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    @if(count($personales_seleccionados) > 0)
                                        <div class="col-lg-12 col-md-2 col-sm-12 mb-3">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead class="table-dark">
                                                    <tr>
                                                        <th>N°</th>
                                                        <th>Nombre</th>
                                                        <th>Apellido</th>
                                                        <th>Correo</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @php $c_p = 1; @endphp
                                                    @foreach($personales_seleccionados as $personal)
                                                        <tr>
                                                            <td>{{$c_p}}</td>
                                                            <td>{{ $personal['personal_nombre'] }}</td>
                                                            <td>{{ $personal['personal_apellido'] }}</td>
                                                            <td>{{ $personal['personal_gmail'] }}</td>
                                                            <td>
                                                                <a class="btn btn-sm btn-danger text-white"
                                                                        wire:click="eliminar_personal({{ $personal['id_personal'] }})">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @php $c_p++; @endphp
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RECURSO -->
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 -col-sm-12 mb-3">
                                <small class="text-primary">Información Recurso</small>
                                <hr class="mb-0">
                            </div>

                            <div class="col-lg-9 col-md-9 col-sm-12 mb-3">
                                <label class="form-label">Recursos</label>
                                <select class="form-control" wire:model="recurso_seleccionado">
                                    <option>Seleccionar...</option>
                                    @foreach($listar_recursos_activos as $lra)
                                        <option value="{{$lra->id_recurso}}"> {{$lra->recurso_nombre}} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3 mt-4">
                                <a class="btn btn-sm bg-primary text-white" wire:click="agregar_recurso">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            </div>

                            <div class="col-lg-12 mb-3">
                                @if (session()->has('success_recurso'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success_recurso') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (session()->has('error_recurso'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error_recurso') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                            </div>

                            @if(count($recursos_seleccionados) > 0)
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                            <tr>
                                                <th>N°</th>
                                                <th>Nombre</th>
                                                <th>Tipo</th>
                                                <th>Medida</th>
                                                <th>Cantidad</th>
                                                <th>Disponible</th>
                                                <th>Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $c_r = 1;@endphp
                                            @foreach($recursos_seleccionados as $recurso)
                                                <tr>
                                                    <td>{{$c_r}}</td>
                                                    <td>{{ $recurso['recurso_nombre'] }}</td>
                                                    <td>{{ $recurso['tipo_recurso_concepto'] }}</td>
                                                    <td>{{ $recurso['medida_nombre'] }}</td>
                                                    <td>
                                                        <div class="input-group" style="max-width: 120px;">
                                                            <input type="text"
                                                                   class="form-control form-control-sm"
                                                                   max="{{ $recurso['recurso_cantidad_maxima'] }}"
                                                                   value="{{ $recurso['cantidad_solicitada'] }}"
                                                                   onkeyup="validar_numeros(this.id)"
                                                                   id="validar_cantidad({{ $recurso['id_recurso'] }}, $event.target.value)"
                                                                   wire:change="validar_cantidad({{ $recurso['id_recurso'] }}, $event.target.value)">
                                                        </div>

                                                        @if (session()->has('error_cantidad_' . $recurso['id_recurso']))
                                                            <small class="text-danger">
                                                                {{ session('error_cantidad_' . $recurso['id_recurso']) }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $recurso['recurso_cantidad_maxima'] }}
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-sm btn-danger text-white"
                                                                wire:click="eliminar_recurso({{ $recurso['id_recurso'] }})">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @php $c_r++;@endphp
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-success text-white">Guardar Registro</button>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal-general>
{{--    FIN MODAL REGISTRAR PERSONAL / RECURSO--}}

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12  mb-2 ">
            <div class="d-flex align-items-center w-100">
                <input type="text" class="form-control w-50 me-4"  wire:model.live="search_registrar_documento" placeholder="Buscar">
                <x-select-filter wire:model.live="pagination_registrar_documento" />
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 text-end mb-2">
            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modal_registrar_documento" >
                <x-slot name="icons">
                    fa-solid fa-plus
                </x-slot>
                Agregar Registrar Documento
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
                                <th>Cliente</th>
                                <th>Serie - Correlativo</th>
                                <th>Fecha Emisión</th>
                                <th>Trabajo a Realizar</th>
                                <th>Modalidad Transporte</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if(count($listar_guias) > 0)
                                @php $conteo = 1; @endphp
                                @foreach($listar_guias as $lv)
                                    <tr>
                                        <td>{{$conteo}}</td>
                                        <td>{{$lv->cliente_razon_social}}</td>
                                        <td>{{$lv->guia_serie}} - {{$lv->guia_correlativo}}</td>
                                        <td>{{$lv->guia_fecha_emision}}</td>
                                        <td>{{$lv->guia_trabajo_realizar}}</td>
                                        <td>
                                            @php
                                            $modalidad = "";
                                            if ($lv->guia_modalidad_transporte == 1){
                                                $modalidad = "Publico";
                                            } elseif ($lv->guia_modalidad_transporte == 2){
                                                $modalidad = "Privado";
                                            } else {
                                                $modalidad = "-";
                                            }
                                            @endphp
                                            {{$modalidad}}
                                        </td>
                                        <td>
                                            @if(!empty($lv->guia_documento))
                                                <a href="{{ asset($lv->guia_documento) }}" target="_blank">
                                                    Ver documento
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-bold badge {{$lv->guia_estado == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$lv->guia_estado == 1 ? 'Habilitado ' : 'Desabilitado'}}
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm bg-primary text-white" wire:click="btn_modal_registrar('{{ base64_encode($lv->id_guia) }}')" data-bs-toggle="modal" data-bs-target="#modal_registrar_personal_recurso">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
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
                @if ($listar_guias->hasPages())
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <x-pagination >
                            <x-slot name="content">
                                {{ $listar_guias->links(data: ['scrollTo' => false]) }}
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
    $wire.on('hide_modal_registrar_documento', () => {
        $('#modal_registrar_documento').modal('hide');
    });

    $wire.on('hide_modal_registrar_personal_recurso', () => {
        $('#modal_registrar_personal_recurso').modal('hide');
    });
</script>
@endscript
