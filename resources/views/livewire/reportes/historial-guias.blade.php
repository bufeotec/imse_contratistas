<div>
    @php
        $general = new \App\Models\General();
    @endphp

    <style>
        .card-soft{border:0;border-radius:14px;box-shadow:0 8px 20px rgba(0,0,0,.05)}
        .card-title-sm{font-weight:700;letter-spacing:.3px;font-size:.95rem}

        .card-soft .card-body{font-size:.88rem}
        .form-label{font-size:.85rem;margin-bottom:.25rem}
        .form-control,.form-select{font-size:.85rem;padding:.25rem .5rem;height:calc(1.9rem + 2px)}
        .input-icon{position:relative}
        .input-icon i{position:absolute;left:10px;top:50%;transform:translateY(-50%);opacity:.6;font-size:.9rem}
        .input-icon input{padding-left:34px}

        .compact-table thead th{font-size:.84rem}
        .compact-table tbody td{font-size:.84rem}
        .compact-table th,.compact-table td{padding:.35rem .5rem}

        .scroll-y{max-height:62vh;overflow:auto}
        @media (max-width:992px){.scroll-y{max-height:40vh}}

        .filters .col-md-3{display:flex;flex-direction:column}
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

    <div class="col-12">
        <div class="loader mt-2 w-100" wire:loading></div>
    </div>

    <div class="card card-soft mb-3">
        <div class="card-body">
            <div class="row g-3 filters">
                <div class="col-md-6">
                    <label class="form-label">Buscar (serie/correlativo o cliente)</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="form-control" placeholder="Ej: T001-000123 o Razon Social" wire:model.live="search">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" class="form-control" wire:model.live="fecha_desde">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" class="form-control" wire:model.live="fecha_hasta">
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft">
        <div class="card-body">
            <div class="table-responsive scroll-y">
                <table class="table table-sm align-middle compact-table">
                    <thead class="table-primary">
                    <tr class="text-center align-middle">
                        <th>N°</th>
                        <th>Fecha emisión</th>
                        <th>Guía</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse ($guias as $g)
                        <tr class="text-center">
                            <td>{{ ($guias->currentPage() - 1) * $guias->perPage() + $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($g->guia_fecha_emision)->format('d/m/Y') }}</td>
                            <td>{{ $g->guia_serie }}-{{ $g->guia_correlativo }}</td>
                            <td>{{ $g->cliente_razon_social }}</td>
                            <td>
                                @if($g->guia_estado == 1)
                                    <span class="badge bg-secondary">Registrado</span>
                                @elseif($g->guia_estado == 2)
                                    <span class="badge bg-primary">Listo para Despacho</span>
                                @elseif($g->guia_estado == 3)
                                    <span class="badge bg-info text-white">Despachado</span>
                                @elseif($g->guia_estado == 4)
                                    <span class="badge bg-success">Sincerado</span>
                                @endif
                                {{--<span class="badge bg-secondary">{{ $g->guia_estado }}</span>--}}
                            </td>
                            <td>
                                <a href="{{route('generar_pdf',['id_guia'=>base64_encode($g->id_guia)])}}" download class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-download"></i>
                                </a>

                                <a class="btn btn-sm btn-outline-secondary" wire:click="btn_info_guia('{{ base64_encode($g->id_guia) }}')" data-bs-toggle="modal" data-bs-target="#modal_info_guia">
                                    <i class="fa-solid fa-circle-info"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No se han encontrado resultados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($guias->hasPages())
                <div class="mt-2 d-flex justify-content-end">
                    {{ $guias->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>
</div>
