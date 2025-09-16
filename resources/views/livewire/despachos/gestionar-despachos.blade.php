<div>
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


    <div class="row g-3">
        {{-- GUÍAS DISPONIBLES --}}
        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 card-title-sm">GUÍAS DISPONIBLES</h5>
                    </div>

                    <div class="input-icon mb-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="form-control" placeholder="Buscar guía">
                    </div>

                    <br>

                    <div class="table-responsive scroll-y">
                        <table class="table table-sm align-middle compact-table">
                            <thead class="table-primary">
                            <tr class="text-center align-middle">
                                <th style="width:50px">#</th>
                                <th>Fecha emisión guía</th>
                                <th>Guía</th>
                                <th>Nombre del cliente</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- Fila 1 (estático) --}}
                            <tr class="text-center">
                                <td>
                                    <button class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="fw-semibold">25 mar. 2025</div>
                                    <div>UBIGEO: <b>LIMA - LIMA - LIMA</b></div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="text-decoration-none">T0010015243</a>
                                </td>
                                <td>INVERSIONES RAPIVENTAS S.A.C.</td>
                            </tr>

                            {{-- Fila 2 --}}
                            <tr class="text-center">
                                <td>
                                    <button class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="fw-semibold">25 mar. 2025</div>
                                    <div>UBIGEO: <b>LIMA - SAN JUAN DE LURIGANCHO</b></div>
                                </td>
                                <td><a href="javascript:void(0)" class="text-decoration-none">T0010015227</a></td>
                                <td>HIPERMERCADOS TOTTUS S.A</td>
                            </tr>

                            {{-- Fila 3 --}}
                            <tr class="text-center">
                                <td>
                                    <button class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="fw-semibold">26 mar. 2025</div>
                                    <div>UBIGEO: <b>LIMA - SAN MARTÍN DE PORRES</b></div>
                                </td>
                                <td><a href="javascript:void(0)" class="text-decoration-none">T0010015260</a></td>
                                <td>RIZOS BELLOS PERÚ E.I.R.L.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- GUÍAS SELECCIONADAS --}}
        <div class="col-lg-6">
            <div class="card card-soft mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Transportistas</label>
                            <select class="form-select">
                                <option value="">Seleccionar...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehículo</label>
                            <select class="form-select">
                                <option value="">Seleccionar...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha de despacho</label>
                            <input type="date" class="form-control" value="{{ now()->format('Y-m-d') }}">
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
                            {{-- Row A (estático) --}}
                            <tr class="text-center">
                                <td>25 mar. 2025</td>
                                <td>T0010015227</td>
                                <td>HIPERMERCADOS TOTTUS S.A</td>
                                <td class="text-start">
                                    DENOMINADO LOTE N°6 DEL EXFUNDO NIEVERÍA LURIGANCHO - CHOSICA<br>
                                    <span>LIMA - SAN JUAN DE LURIGANCHO</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>

                            {{-- Row B --}}
                            <tr class="text-center">
                                <td>26 mar. 2025</td>
                                <td>T0010015260</td>
                                <td>RIZOS BELLOS PERÚ E.I.R.L.</td>
                                <td class="text-start">
                                    MZA. R LOTE 11 URB. LOS LIBERTADORES<br>
                                    <span>LIMA - SAN MARTÍN DE PORRES</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>

                            {{-- Row C --}}
                            <tr class="text-center">
                                <td>25 mar. 2025</td>
                                <td>T0010015233</td>
                                <td>IMPORTACIONES EUROSMARK E.I.R.L.</td>
                                <td class="text-start">
                                    JR PUNO 952 MERCADO CENTRAL - LIMA<br>
                                    <span>LIMA - LIMA - LIMA</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="sticky-footer mt-4 d-flex justify-content-end">
                <button class="btn btn-danger px-4">
                    Guardar Despacho
                </button>
            </div>
        </div>
    </div>
</div>
