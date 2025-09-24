<div>
    @php
        $general = new \App\Models\General();
    @endphp

    <style>
        .card-soft{border:0;border-radius:14px;box-shadow:0 8px 20px rgba(0,0,0,.05)}
        .card-title-sm{font-weight:700;letter-spacing:.3px;font-size:.95rem}

        /* Compactar fuentes y paddings */
        .card-soft .card-body{font-size:.88rem}
        .form-label{font-size:.85rem;margin-bottom:.25rem}
        .form-control,.form-select{font-size:.85rem;padding:.25rem .5rem;height:calc(1.9rem + 2px)}
        .input-icon{position:relative}
        .input-icon i{position:absolute;left:10px;top:50%;transform:translateY(-50%);opacity:.6;font-size:.9rem}
        .input-icon input{padding-left:34px}

        /* Tablas más apretadas */
        .compact-table thead th{font-size:.84rem}
        .compact-table tbody td{font-size:.84rem}
        .compact-table th,.compact-table td{padding:.35rem .5rem}

        .scroll-y{max-height:62vh;overflow:auto}
        @media (max-width:992px){.scroll-y{max-height:40vh}}

        /* Botón guardar flotante */
        .sticky-footer{position:sticky;bottom:-1px;padding:12px 0;margin-top:10px}
    </style>

    {{-- MODAL INFORMACIÓN GUÍA --}}
    <x-modal-general wire:ignore.self>
        <x-slot name="id_modal">modal_info_guia</x-slot>
        <x-slot name="tama">modal-xl</x-slot>
        <x-slot name="titleModal">Información de la Guía</x-slot>

        <x-slot name="modalContent">
            <style>
                .mini-card{background:#f8f9fa;border:1px solid #eef2f7;border-radius:12px;padding:10px;height:100%}
                .chip{display:inline-block;font-size:.75rem;padding:.25rem .5rem;border-radius:999px;border:1px solid #e5e7eb;background:#fff}
                .section-title{font-weight:700;letter-spacing:.3px;font-size:.9rem;margin-bottom:6px}
                .divider{margin:.5rem 0 1rem 0;border-top:1px solid #eef2f7}
                .kv .label{font-size:.8rem;color:#6b7280;margin-bottom:2px}
                .kv .value{font-weight:600}
            </style>

            @if($listar_info_guia)
                @php
                    $i_modalidad = $listar_info_guia->guia_modalidad_transporte == 1 ? 'Público' : ($listar_info_guia->guia_modalidad_transporte == 2 ? 'Privado' : '-');
                @endphp

                <div class="mini-card mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="chip"><i class="fa-regular fa-file-lines me-1"></i> Guía:
                                <strong class="ms-1">{{ $listar_info_guia->guia_serie }}-{{ $listar_info_guia->guia_correlativo }}</strong>
                            </span>
                                <span class="chip"><i class="fa-regular fa-calendar me-1"></i>
                                {{ $listar_info_guia->guia_fecha_emision ? $general->obtenerNombreFecha($listar_info_guia->guia_fecha_emision, 'Date', 'Date') : '-' }}
                            </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                        <span class="badge {{ $i_modalidad==='Público' ? 'bg-success' : ($i_modalidad==='Privado' ? 'bg-primary' : 'bg-secondary') }}">
                            {{ $i_modalidad }}
                        </span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-lg-6">
                        <div class="mini-card">
                            <div class="section-title">Datos generales</div>
                            <div class="divider"></div>
                            <div class="row g-2 kv">
                                <div class="col-12">
                                    <div class="label">Trabajo a realizar</div>
                                    <div class="value">{{ $listar_info_guia->guia_trabajo_realizar }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="label">Serie - Correlativo</div>
                                    <div class="value">{{ $listar_info_guia->guia_serie }} - {{ $listar_info_guia->guia_correlativo }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="label">Fecha de emisión</div>
                                    <div class="value">
                                        {{ $listar_info_guia->guia_fecha_emision ? $general->obtenerNombreFecha($listar_info_guia->guia_fecha_emision, 'Date', 'Date') : '-' }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="label">Modalidad de transporte</div>
                                    <div class="value">{{ $i_modalidad }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mini-card">
                            <div class="section-title">Datos del cliente</div>
                            <div class="divider"></div>
                            <div class="row g-2 kv">
                                <div class="col-12">
                                    <div class="label">Nombre / Razón social</div>
                                    <div class="value">{{ $listar_info_guia->cliente_razon_social }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="label">RUC / DNI</div>
                                    <div class="value">{{ $listar_info_guia->cliente_numero_documento }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="label">Teléfono</div>
                                    <div class="value">{{ $listar_info_guia->cliente_telefono ?: '—' }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="label">Dirección</div>
                                    <div class="value">{{ $listar_info_guia->cliente_direccion }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="label">Correo</div>
                                    <div class="value">{{ $listar_info_guia->cliente_email ?: '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="mini-card">
                            <div class="section-title">Recursos</div>
                            <div class="divider"></div>

                            @if($listar_recursos && $listar_recursos->count())
                                <x-table-general>
                                    <x-slot name="thead">
                                        <tr class="text-center">
                                            <th style="width:60px">N°</th>
                                            <th>Recurso</th>
                                            <th>Tipo Recurso</th>
                                            <th>Medida</th>
                                            <th style="width:100px">Cantidad</th>
                                        </tr>
                                    </x-slot>
                                    <x-slot name="tbody">
                                        @foreach($listar_recursos as $lr)
                                            <tr class="text-center">
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $lr->recurso_nombre }}</td>
                                                <td>{{ $lr->tipo_recurso_concepto }}</td>
                                                <td>{{ $lr->medida_nombre }}</td>
                                                <td class="text-end">{{ $lr->guia_recurso_cantidad }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                </x-table-general>
                            @else
                                <p class="text-muted m-0">No hay recursos registrados para esta guía.</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mini-card">
                            <div class="section-title">Personales</div>
                            <div class="divider"></div>

                            @if($listar_personales && $listar_personales->count())
                                <x-table-general>
                                    <x-slot name="thead">
                                        <tr class="text-center">
                                            <th style="width:60px">N°</th>
                                            <th>Nombre</th>
                                            <th>Apellidos</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                        </tr>
                                    </x-slot>
                                    <x-slot name="tbody">
                                        @foreach($listar_personales as $lp)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $lp->personal_nombre }}</td>
                                                <td>{{ $lp->personal_apellido }}</td>
                                                <td>{{ $lp->personal_gmail }}</td>
                                                <td>{{ $lp->personal_telefono }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                </x-table-general>
                            @else
                                <p class="text-muted m-0">No hay personales registrados para esta guía.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
                </div>
            @endif
        </x-slot>
    </x-modal-general>
    {{-- FIN MODAL INFORMACIÓN GUÍA --}}

    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="loader mt-2 w-100" wire:loading wire:target="seleccionar, quitar, transportista_id, guardar_despacho"></div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible show fade">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible show fade">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="row g-3">
        {{-- GUÍAS DISPONIBLES --}}
        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0 card-title-sm">GUÍAS DISPONIBLES</h5>
                    </div>

                    <div class="input-icon mb-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="form-control" placeholder="Buscar por cliente, serie o correlativo" wire:model.live="search_guia">
                    </div>

                    <div class="table-responsive scroll-y">
                        <table class="table table-sm align-middle compact-table">
                            <thead class="table-primary">
                            <tr class="text-center align-middle">
                                <th style="width:56px">Sel.</th>
                                <th>Fecha emisión guía</th>
                                <th>Guía</th>
                                <th>Nombre del cliente</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($guias_disponibles as $g)
                                <tr class="text-center">
                                    <td>
                                        <button class="btn btn-sm btn-success" title="Seleccionar" wire:click="seleccionar('{{ base64_encode($g->id_guia) }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($g->guia_fecha_emision)->format('d/m/Y') }}</td>
                                    <td>
                                       {{-- <a href="javascript:void(0)" class="text-decoration-none">
                                            {{ $g->guia_serie }}-{{ $g->guia_correlativo }}
                                        </a>--}}

                                        <a href="{{route('generar_pdf',['id_guia'=>base64_encode($g->id_guia)])}}" target="_blank" class="text-decoration-none" >
                                            {{ $g->guia_serie }}-{{ $g->guia_correlativo }}
                                        </a>
                                    </td>
                                    <td>{{ $g->cliente_razon_social }}</td>
                                    <td>
                                        <a class="btn btn-sm bg-info text-white my-1 ms-2" wire:click="btn_info_guia('{{ base64_encode($g->id_guia) }}')" data-bs-toggle="modal" data-bs-target="#modal_info_guia">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No se han encontrado resultados.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($guias_disponibles->hasPages())
                        <div class="mt-2 d-flex justify-content-end">
                            {{ $guias_disponibles->links(data: ['scrollTo' => false]) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- GUÍAS SELECCIONADAS --}}
        <div class="col-lg-6">
            <div class="card card-soft mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Transportistas</label>
                            <select class="form-select" wire:model.live="transportista_id">
                                <option value="">Seleccionar...</option>
                                @foreach($listar_transportistas as $t)
                                    <option value="{{ $t->id_transportista }}">{{ $t->transportista_razon_social }}</option>
                                @endforeach
                            </select>
                            @error('transportista_id') <span class="message-error d-block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Vehículo</label>
                            <select class="form-select" wire:model.live="vehiculo_id" @disabled(!$transportista_id)>
                                <option value="">Seleccionar...</option>
                                @foreach($listar_vehiculos as $v)
                                    <option value="{{ $v->id_vehiculo }}">{{ $v->vehiculo_placa }}</option>
                                @endforeach
                            </select>
                            @error('vehiculo_id') <span class="message-error d-block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">N° Orden</label>
                            <input type="text" class="form-control" wire:model="despacho_nr_orden">
                            @error('despacho_nr_orden') <span class="message-error d-block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" wire:model="fecha_despacho">
                            @error('fecha_despacho') <span class="message-error d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-soft">
                <div class="card-body">
                    <h6 class="card-title-sm mb-3">Guías Seleccionadas</h6>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle compact-table">
                            <thead class="table-primary">
                            <tr class="text-center align-middle">
                                <th>Fecha emisión guía</th>
                                <th>Guía</th>
                                <th>Nombre del cliente</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($guias_seleccionadas) > 0)
                                @foreach($guias_seleccionadas as $row)
                                    <tr class="text-center">
                                        <td>{{ \Carbon\Carbon::parse($row['fecha'])->format('d/m/Y') }}</td>
                                        <td>
                                            {{--{{ $row['guia'] }}--}}

                                            <a href="{{route('generar_pdf',['id_guia'=>base64_encode($g->id_guia)])}}" target="_blank" class="text-decoration-none" >
                                                {{ $row['guia'] }}
                                            </a>
                                        </td>
                                        <td>{{ $row['cliente'] }}</td>
                                        <td>
                                            {{ $row['direccion'] }}
                                        </td>
                                        <td>
                                            <a class="btn btn-sm bg-info text-white" wire:click="btn_info_guia('{{ base64_encode($g->id_guia) }}')" data-bs-toggle="modal" data-bs-target="#modal_info_guia">
                                                <i class="fa-solid fa-circle-info"></i>
                                            </a>

                                            <button class="btn btn-sm btn-danger" title="Quitar" wire:click="quitar('{{ base64_encode($row['id_guia']) }}')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No hay guías seleccionadas.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="sticky-footer mt-4 d-flex justify-content-end">
                <button class="btn btn-primary px-4" wire:click="guardar_despacho">
                    Guardar Despacho
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // abrir el PDF en nueva pestaña
    $wire.on('openPdf', (e) => {
        const url = e?.url || e;
        if (url) window.open(url, '_blank');
    });
</script>
@endscript
