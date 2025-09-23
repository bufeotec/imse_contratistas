<div>

    @php
        $general = new \App\Models\General();
    @endphp

    {{-- MODAL REGISTRAR DOCUMENTO--}}
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
                        <!-- CLIENTE -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                <small class="text-primary">Información del Cliente</small>
                                <hr class="mb-0">
                            </div>

                            <div class="col-6 col-md-6 col-sm-12 mb-3">
                                <x-input-general type="hidden" id="id_cliente" wire:model="id_cliente" />
                                <label for="id_tipo_documento" class="form-label">Tipo de documento <b class="text-danger">(*)</b></label>
                                <select
{{--                                    @if(!$mostrar) disabled @endif--}}
                                id="id_tipo_documento" wire:model="id_tipo_documento" class="form-select">
                                    <option value="">Seleccionar</option>
                                    @foreach($listar_tipo_doc as $ltd)
                                        <option value="{{$ltd->id_tipo_documento}}">{{$ltd->tipo_documento_identidad_abr}}</option>
                                    @endforeach
                                </select>
                                @error('id_tipo_documento')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-6 col-md-6 col-sm-12 mb-3">
                                <label for="cliente_numero_documento" class="form-label">Número de documento <b class="text-danger">(*)</b></label>
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

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="cliente_razon_social" class="form-label">Razón Social <b class="text-danger">(*)</b></label>
                                <x-input-general type="text" id="cliente_razon_social" wire:model="cliente_razon_social" />
                                @error('cliente_razon_social')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="cliente_nombre_comercial" class="form-label">Nombre Comercial <b class="text-danger">(*)</b></label>
                                <x-input-general  type="text" id="cliente_nombre_comercial" wire:model="cliente_nombre_comercial" />
                                @error('cliente_nombre_comercial')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="cliente_telefono" class="form-label">Celular</label>
                                <x-input-general  type="text" id="cliente_telefono" wire:model="cliente_telefono" onkeyup="validar_numeros(this.id)" />
                                @error('cliente_telefono')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="cliente_email" class="form-label">Email</label>
                                <x-input-general  type="text" id="cliente_email" wire:model="cliente_email" />
                                @error('cliente_email')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="cliente_direccion" class="form-label">Dirección <b class="text-danger">(*)</b></label>
                                <textarea id="cliente_direccion" name="cliente_direccion" rows="2" wire:model="cliente_direccion" class="form-control"></textarea>
                                @error('cliente_direccion')<span class="message-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <!-- PERSONALES -->
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

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="loader mt-2 w-100" wire:loading wire:target="agregar_personal, eliminar_personal"></div>
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

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <!-- GUÍA -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                <small class="text-primary">Información de la guia</small>
                                <hr class="mb-0">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_fecha_emision" class="form-label">Fecha de Emisión <b class="text-danger">(*)</b></label>
                                <x-input-general type="date" id="guia_fecha_emision" wire:model="guia_fecha_emision" />
                                @error('guia_fecha_emision')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="guia_modalidad_transporte" class="form-label">Modalidad de Transporte <b class="text-danger">(*)</b></label>
                                <select class="form-control" id="guia_modalidad_transporte" wire:model="guia_modalidad_transporte">
                                    <option value="">Seleccionar...</option>
                                    <option value="1">Publico</option>
                                    <option value="2">Privado</option>
                                </select>
                                @error('guia_modalidad_transporte')<span class="message-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                <label for="guia_trabajo_realizar" class="form-label">Trabajo a realizar <b class="text-danger">(*)</b></label>
                                <textarea class="form-control" rows="2" id="guia_trabajo_realizar" wire:model="guia_trabajo_realizar"></textarea>
                                @error('guia_trabajo_realizar')<span class="message-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <!-- RECURSO -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 -col-sm-12 mb-3">
                                <small class="text-primary">Información Recurso</small>
                                <hr class="mb-0">
                            </div>

                            <div class="col-lg-9 col-md-9 col-sm-12 mb-3 position-relative">
                                <label class="form-label">Recursos</label>
                                <input type="text"
                                       class="form-control"
                                       placeholder="Buscar"
                                       wire:model="buscar_recurso_search"
                                       wire:keyup="buscar_recurso_filtro()">
                                @if($abrir_lista_recurso)
                                    <div style="width: 120%; z-index: 999" class="position-absolute top-100 start-0 mt-1 z-10" id="lista_cliente_reporte">
                                        <div class="list-group bg-white shadow-sm">
                                            @foreach($lista_recurso_filtro as $l)
                                                <a style="cursor: pointer" class="list-group-item list-group-item-action"
                                                   wire:click="seleccionar_recurso_vista('{{base64_encode($l->id_recurso)}}')">
                                                    {{ $l->recurso_nombre }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3 mt-4">
                                <a class="btn btn-sm bg-primary text-white" wire:click="agregar_recurso">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="loader mt-2 w-100" wire:loading wire:target="agregar_recurso, eliminar_recurso, buscar_recurso_filtro, seleccionar_recurso_vista"></div>
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
    {{-- FIN MODAL REGISTRAR DOCUMENTO--}}

    {{-- MODAL INFORMACIÓN GUÍA--}}
    <x-modal-general wire:ignore.self >
        <x-slot name="id_modal">modal_info_guia</x-slot>
        <x-slot name="tama">modal-xl</x-slot>
        <x-slot name="titleModal">Información de la Guía</x-slot>
        <x-slot name="modalContent">
            @if($listar_info_guia)
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h6>Datos generales</h6>
                                <hr>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 mb-3">
                                <strong class="color_imse mb-2">Trabajo a Realizar</strong>
                                <p>{{ $listar_info_guia->guia_trabajo_realizar }}</p>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                <strong class="color_imse mb-2">Serie - Correlativo</strong>
                                <p>{{ $listar_info_guia->guia_serie }} - {{ $listar_info_guia->guia_correlativo }}</p>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                <strong class="color_imse mb-2">Fecha de Emisión</strong>
                                <p>{{ $listar_info_guia->guia_fecha_emision ? $general->obtenerNombreFecha($listar_info_guia->guia_fecha_emision, 'Date', 'Date') : '-' }}</p>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 mb-3">
                                @php
                                $i_modalidad = "";
                                if ($listar_info_guia->guia_modalidad_transporte == 1){
                                    $i_modalidad = "Publico";
                                } elseif ($listar_info_guia->guia_modalidad_transporte == 2){
                                    $i_modalidad = "Privado";
                                } else {
                                    $i_modalidad = "-";
                                }
                                @endphp
                                <strong class="color_imse mb-2">Modalidad de Transporte</strong>
                                <p>{{ $i_modalidad }}</p>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h6>Datos del Cliente</h6>
                                <hr>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 mb-3">
                                <strong class="color_imse mb-2">Nombre del Cliente</strong>
                                <p>{{ $listar_info_guia->cliente_razon_social }}</p>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                <strong class="color_imse mb-2">RUC / DNI</strong>
                                <p>{{ $listar_info_guia->cliente_numero_documento }}</p>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 mb-3">
                                <strong class="color_imse mb-2">Dirección</strong>
                                <p>{{ $listar_info_guia->cliente_direccion }}</p>
                            </div>

                            @if(!empty($listar_info_guia->cliente_telefono))
                                <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                    <strong class="color_imse mb-2">Teléfono</strong>
                                    <p>{{ $listar_info_guia->cliente_telefono }}</p>
                                </div>
                            @endif

                           @if(!empty($listar_info_guia->cliente_email))
                                <div class="col-lg-2 col-md-3 col-sm-4 mb-3">
                                    <strong class="color_imse mb-2">Correo</strong>
                                    <p>{{ $listar_info_guia->cliente_email }}</p>
                                </div>
                           @endif
                        </div>

                        <div class="row">
                            <!-- RECURSOS -->
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                @if($listar_recursos && $listar_recursos->count())
                                    <h6>Recursos</h6>
                                    <hr>
                                    <x-table-general>
                                        <x-slot name="thead">
                                            <tr>
                                                <th>N°</th>
                                                <th>Recurso</th>
                                                <th>Tipo Recurso</th>
                                                <th>Medida</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </x-slot>
                                        <x-slot name="tbody">
                                            @php $conreo_r = 1; @endphp
                                            @foreach($listar_recursos as $lr)
                                                <tr>
                                                    <td>{{ $conreo_r }}</td>
                                                    <td>{{ $lr->recurso_nombre }}</td>
                                                    <td>{{ $lr->tipo_recurso_concepto }}</td>
                                                    <td>{{ $lr->medida_nombre }}</td>
                                                    <td>{{ $lr->guia_recurso_cantidad }}</td>
                                                </tr>
                                                @php $conreo_r++; @endphp
                                            @endforeach
                                        </x-slot>
                                    </x-table-general>
                                @else
                                    <p class="text-muted mt-4 ms-2">No hay recursos registrados para esta guía.</p>
                                @endif
                            </div>

                            <!-- PERSONALES -->
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                @if($listar_personales && $listar_personales->count())
                                    <h6>Personales</h6>
                                    <hr>
                                    <x-table-general>
                                        <x-slot name="thead">
                                            <tr>
                                                <th>N°</th>
                                                <th>Nombre</th>
                                                <th>Apellidos</th>
                                                <th>Correo</th>
                                                <th>Teléfono</th>
                                            </tr>
                                        </x-slot>
                                        <x-slot name="tbody">
                                            @php $conreo_p = 1; @endphp
                                            @foreach($listar_personales as $lp)
                                                <tr>
                                                    <td>{{ $conreo_p }}</td>
                                                    <td>{{ $lp->personal_nombre }}</td>
                                                    <td>{{ $lp->personal_apellido }}</td>
                                                    <td>{{ $lp->personal_gmail }}</td>
                                                    <td>{{ $lp->personal_telefono }}</td>
                                                </tr>
                                                @php $conreo_p++; @endphp
                                            @endforeach
                                        </x-slot>
                                    </x-table-general>
                                @else
                                    <p class="text-muted mt-4 ms-2">No hay personales registrados para esta guía.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3 text-end">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                    </div>
                </div>
            @endif

        </x-slot>
    </x-modal-general>
    {{-- FIN MODAL INFORMACIÓN GUÍA--}}

    {{-- MODAL PASAR A PROGRAMAR DESPACHO--}}
    <x-modal-delete wire:ignore.self>
        <x-slot name="id_modal">modal_registrar_personal_recurso</x-slot>
        <x-slot name="modalContentDelete">
            <form wire:submit.prevent="guia_pasar_programar_despacho">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h2 class="deleteTitle">¿Confirmas pasar la guía a Programación de despacho?</h2>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @error('id_guia') <span class="message-error">{{ $message }}</span> @enderror

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
    {{-- FIN MODAL PASAR A PROGRAMAR DESPACHO--}}

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
                Crear Documento
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
                                            <span class="font-bold badge {{$lv->guia_estado == 1 ? 'bg-label-success ' : 'bg-label-danger'}}">
                                                {{$lv->guia_estado == 1 ? 'Habilitado ' : 'Desabilitado'}}
                                            </span>
                                        </td>
                                        <td>
                                            <!-- BTN DE INFORMACIÓN DE LA GUÍA -->
                                            <a class="btn btn-sm bg-info text-white my-1 ms-2" wire:click="btn_info_guia('{{ base64_encode($lv->id_guia) }}')" data-bs-toggle="modal" data-bs-target="#modal_info_guia">
                                                <i class="fa-solid fa-circle-info"></i>
                                            </a>

                                            <!-- BTN DESCARGAR LA GUÍA -->
                                            <a href="{{route('generar_pdf',['id_guia'=>base64_encode($lv->id_guia)])}}" target="_blank" class="btn btn-sm bg-danger text-white my-1 ms-2" >
                                                <i class="fa-solid fa-download"></i>
                                            </a>

                                            <!-- BTN CONFIRMAR SERVICIO -->
                                            <a class="btn btn-sm bg-primary text-white my-1 ms-2" wire:click="btn_modal_registrar('{{ base64_encode($lv->id_guia) }}')" data-bs-toggle="modal" data-bs-target="#modal_registrar_personal_recurso">
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
