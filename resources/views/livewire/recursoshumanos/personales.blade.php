<div>
    {{-- MODAL CREAR / EDITAR --}}
    <x-modal-general wire:ignore.self>
        <x-slot name="id_modal">modal_personal</x-slot>
        <x-slot name="titleModal">Gestionar personal</x-slot>
        <x-slot name="modalContent">
            <form wire:submit.prevent="save_personal">
                <div class="row">
                    <x-input-general type="hidden" id="id_personal" wire:model="id_personal" />

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="personal_nombre" class="form-label">Nombres (*)</label>
                        <x-input-general type="text" id="personal_nombre" wire:model="personal_nombre" />
                        @error('personal_nombre')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="personal_apellido" class="form-label">Apellidos (*)</label>
                        <x-input-general type="text" id="personal_apellido" wire:model="personal_apellido" />
                        @error('personal_apellido')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="personal_gmail" class="form-label">Email (*)</label>
                        <x-input-general type="text" id="personal_gmail" wire:model="personal_gmail" />
                        @error('personal_gmail')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <label for="personal_telefono" class="form-label">Celular (*)</label>
                        <x-input-general type="text" id="personal_telefono" wire:model="personal_telefono" onkeyup="validar_numeros(this.id)" />
                        @error('personal_telefono')<span class="message-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <label for="personal_direccion" class="form-label">Dirección (*)</label>
                        <textarea id="personal_direccion" rows="3" class="form-control" wire:model="personal_direccion"></textarea>
                        @error('personal_direccion')<span class="message-error">{{ $message }}</span>@enderror
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
    {{-- FIN MODAL --}}

    <div class="row">
        <div class="col-lg-4 col-md-3 col-sm-12 mt-4 mb-2">
            <div class="d-flex align-items-center w-100">
                <input type="text" class="form-control w-50 me-4" wire:model.live="search_personal" placeholder="Buscar">
                <x-select-filter wire:model.live="pagination_personal" />
            </div>
        </div>

        <div class="col-lg-5"></div>

        <div class="col-lg-3 text-end mt-4 mb-2">
            <x-btn-export wire:click="clear_form" class="bg-success text-white" data-bs-toggle="modal" data-bs-target="#modal_personal">
                <x-slot name="icons">fa-solid fa-plus</x-slot>
                Agregar Personal
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
                <div class="col-lg-12">
                    <x-table-general>
                        <x-slot name="thead">
                            <tr class="text-center">
                                <th>N°</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if(count($listar_personales) > 0)
                                @php $i=1; @endphp
                                @foreach($listar_personales as $p)
                                    <tr class="text-center">
                                        <td>{{ $i }}</td>
                                        <td>{{ $p->personal_nombre }}</td>
                                        <td>{{ $p->personal_apellido }}</td>
                                        <td>{{ $p->personal_gmail }}</td>
                                        <td>{{ $p->personal_telefono }}</td>
                                        <td>
                                            <span class="font-bold badge {{ $p->personal_estado == 1 ? 'bg-label-success' : 'bg-label-danger' }}">
                                                {{ $p->personal_estado == 1 ? 'Habilitado' : 'Deshabilitado' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm bg-warning text-white ms-2"
                                               wire:click="editar('{{ base64_encode($p->id_personal) }}')"
                                               data-bs-toggle="modal" data-bs-target="#modal_personal">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @php $i++; @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No se han encontrado resultados.</td>
                                </tr>
                            @endif
                        </x-slot>
                    </x-table-general>
                </div>

                @if ($listar_personales->hasPages())
                    <div class="col-12">
                        <x-pagination>
                            <x-slot name="content">
                                {{ $listar_personales->links(data: ['scrollTo' => false]) }}
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
        $('#modal_personal').modal('hide');
    });
</script>
@endscript
