<div>
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
        .row-toggle{cursor:pointer}
        .badge-soft{background:#eef2ff;color:#3730a3;font-weight:600;border-radius:10px;padding:.15rem .45rem}
        .sticky-footer{position:sticky;bottom:-1px;padding:12px 0;margin-top:10px;background:#fff}
        .child-row{background:#fafafa}
        .child-box{border:1px solid #eee;border-radius:10px;padding:10px}
    </style>

    <div class="col-12">
        <div class="loader mt-2 w-100" wire:loading wire:target="toggleExpand, sincera, page"></div>
    </div>

    <div class="col-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible show fade">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible show fade">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <div class="card card-soft mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Buscar (cliente / serie / correlativo / servicio)</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="form-control" placeholder="Ej: ACME / T001-000123 / Pintado" wire:model.live="search">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" class="form-control" wire:model.live="fecha_desde">
                </div>
                <div class="col-lg-3">
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
                        <th>Fecha</th>
                        <th>Guía</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th style="width:50px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($guias as $g)
                        <tr class="text-center row-toggle" wire:click="toggleExpand('{{ base64_encode($g->id_guia) }}')" wire:key="g-{{ $g->id_guia }}">
                            <td>{{ \Carbon\Carbon::parse($g->guia_fecha_emision)->format('d/m/Y') }}</td>
                            <td>{{ $g->guia_serie }}-{{ $g->guia_correlativo }}</td>
                            <td>{{ $g->cliente_razon_social }}</td>
                            <td>{{ $g->guia_trabajo_realizar }}</td>
                            <td>
                                <i class="fa-solid {{ !empty($expanded[$g->id_guia]) ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                            </td>
                        </tr>

                        {{-- acordeón recursos --}}
                        @if(!empty($expanded[$g->id_guia]))
                            <tr class="child-row" wire:key="gc-{{ $g->id_guia }}" style="background-color: #9aa5b3; color: white">
                                <td colspan="5">
                                    <div class="child-box">
                                        @php
                                            $recursos = $recursosPorGuia[$g->id_guia] ?? [];
                                        @endphp

                                        @if(empty($recursos))
                                            <p class="text-muted mb-0">No hay recursos para esta guía.</p>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead class="table-light">
                                                    <tr class="text-center align-middle">
                                                        <th style="width:177px; background-color: #9aa5b3; color: white">N°</th>
                                                        <th class="text-center" style="background-color: #9aa5b3; color: white">Recurso</th>
                                                        <th style="width:120px; background-color: #9aa5b3; color: white">Seleccionar</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($recursos as $idx => $r)
                                                        <tr class="text-center align-middle" wire:key="r-{{ $g->id_guia }}-{{ $r['id_recurso'] }}" style="color: white">
                                                            <td>{{ $idx + 1 }}</td>
                                                            <td class="text-center">{{ $r['recurso_nombre'] }}</td>
                                                            <td>
                                                                <input type="checkbox" class="form-check-input" wire:model="seleccionados.{{ $g->id_guia }}" value="{{ $r['id_recurso'] }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No se han encontrado guías.</td>
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

            <div class="sticky-footer d-flex justify-content-end">
                <button class="btn btn-primary px-4" wire:click="sincerar">
                    Sincerar Documentos
                </button>
            </div>
        </div>
    </div>
</div>
