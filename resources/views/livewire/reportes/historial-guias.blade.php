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

        .filters .col-md-3{display:flex;flex-direction:column}
    </style>

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
                            <td><span class="badge bg-secondary">{{ $g->guia_estado }}</span></td>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary" title="Descargar">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary" title="Info">
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
