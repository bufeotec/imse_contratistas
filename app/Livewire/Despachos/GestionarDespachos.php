<?php

namespace App\Livewire\Despachos;

use App\Models\General;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Logs;
use App\Models\Despacho;
use App\Models\DespachoDetalle;
use App\Models\DespachoDetalleRecurso;

class GestionarDespachos extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    private $logs;

    private $general;

    public function __construct()
    {
        $this->logs = new Logs();
        $this->general = new General();
    }

    // Filtros / selects
    public $search_guia = '';
    public $pagination_guias = 10;

    public $transportista_id = '';
    public $vehiculo_id = '';
    public $fecha_despacho;
    public $despacho_nr_orden;

    public $guias_seleccionadas = [];
    public $guias_ids_seleccionadas = [];

    public function mount()
    {
        if (!$this->fecha_despacho) {
            $this->fecha_despacho = now()->format('Y-m-d');
        }
    }

    public function render()
    {
        try {
            $s = trim($this->search_guia);

            $query = DB::table('guias as g')
                ->join('clientes as c', 'c.id_cliente', '=', 'g.id_cliente')
                ->select(
                    'g.id_guia',
                    'g.guia_serie',
                    'g.guia_correlativo',
                    'g.guia_fecha_emision',
                    'g.guia_documento',
                    'c.cliente_razon_social'
                )
                ->where('g.guia_estado', 2);

            // Excluir las que ya fueron seleccionadas
            if (!empty($this->guias_ids_seleccionadas)) {
                $query->whereNotIn('g.id_guia', $this->guias_ids_seleccionadas);
            }

            // Búsqueda por cliente, serie o correlativo
            if ($s !== '') {
                $query->where(function ($w) use ($s) {
                    $w->where('c.cliente_razon_social', 'like', "%{$s}%")
                        ->orWhere('g.guia_serie', 'like', "%{$s}%")
                        ->orWhere('g.guia_correlativo', 'like', "%{$s}%");
                });
            }

            $guias_disponibles = $query
                ->orderByDesc('g.guia_fecha_emision')
                ->orderByDesc('g.id_guia')
                ->paginate($this->pagination_guias);

            // Para los transportistas y vehículos
            $listar_transportistas = DB::table('transportistas')
                ->select('id_transportista','transportista_razon_social')
                ->when(DB::getSchemaBuilder()->hasColumn('transportistas','transportista_estado'), function($q){
                    $q->where('transportista_estado', 1);
                })
                ->orderBy('transportista_razon_social')
                ->get();

            $listar_vehiculos = collect();
            if (!empty($this->transportista_id)) {
                $listar_vehiculos = DB::table('vehiculos')
                    ->select('id_vehiculo','vehiculo_placa')
                    ->where('id_transportista', $this->transportista_id)
                    ->when(DB::getSchemaBuilder()->hasColumn('vehiculos','vehiculo_estado'), function($q){
                        $q->where('vehiculo_estado', 1);
                    })
                    ->orderBy('vehiculo_placa')
                    ->get();
            }

            return view('livewire.despachos.gestionar-despachos', compact('guias_disponibles', 'listar_transportistas', 'listar_vehiculos'));
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            $guias_disponibles = collect();
            $listar_transportistas = collect();
            $listar_vehiculos = collect();
            return view('livewire.despachos.gestionar-despachos', compact('guias_disponibles', 'listar_transportistas', 'listar_vehiculos'));
        }
    }

    public function seleccionar($id_b64)
    {
        try {
            $id = base64_decode($id_b64);

            if (in_array($id, $this->guias_ids_seleccionadas, true)) {
                return;
            }

            $g = DB::table('guias as g')
                ->join('clientes as c', 'c.id_cliente', '=', 'g.id_cliente')
                ->select(
                    'g.id_guia',
                    'g.guia_serie',
                    'g.guia_correlativo',
                    'g.guia_fecha_emision',
                    'g.guia_documento',
                    'c.cliente_razon_social',
                    'c.cliente_direccion'
                )
                ->where('g.id_guia', $id)
                ->first();

            if (!$g) { return; }

            $this->guias_ids_seleccionadas[] = $g->id_guia;

            $this->guias_seleccionadas[$g->id_guia] = [
                'id_guia' => $g->id_guia,
                'guia' => $g->guia_serie . '-' . $g->guia_correlativo,
                'fecha' => $g->guia_fecha_emision,
                'cliente' => $g->cliente_razon_social,
                'direccion' => $g->cliente_direccion,
            ];
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
        }
    }

    public function quitar($id_b64)
    {
        try {
            $id = base64_decode($id_b64);

            if (isset($this->guias_seleccionadas[$id])) {
                unset($this->guias_seleccionadas[$id]);
            }

            $this->guias_ids_seleccionadas = array_values(
                array_filter($this->guias_ids_seleccionadas, fn($x) => (int)$x !== (int)$id)
            );
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
        }
    }

    public function updatedTransportistaId()
    {
        $this->vehiculo_id = '';
    }

    public function guardar_despacho()
    {
        try {
            $this->validate([
                'transportista_id' => 'required|integer',
                'vehiculo_id' => 'required|integer',
                'fecha_despacho' => 'required|date',
                'despacho_nr_orden' => 'required|string',
            ], [
                'transportista_id.required' => 'Seleccione un transportista.',
                'vehiculo_id.required' => 'Seleccione un vehículo.',
                'fecha_despacho.required' => 'Seleccione la fecha de despacho.',
                'despacho_nr_orden.required' => 'Ingrese un número de orden.',
            ]);

            if (count($this->guias_seleccionadas) === 0) {
                session()->flash('error', 'Debe seleccionar al menos una guía.');
                return;
            }

            DB::beginTransaction();

            // ===== DESPACHO =====
            $desp = new Despacho();
            $desp->id_transportista = $this->transportista_id;
            $desp->id_vehiculo = $this->vehiculo_id;
            $desp->despacho_fecha = $this->fecha_despacho . ' 00:00:00';
            $desp->despacho_nr_orden = $this->despacho_nr_orden;
            $desp->despacho_estado = 1;

            if (!$desp->save()) {
                DB::rollBack();
                session()->flash('error', 'Ocurrió un error al guardar el despacho.');
                return;
            }

            $id_despacho = $desp->id_despacho;

            // ===== DETALLE =====
            foreach ($this->guias_seleccionadas as $g) {
                $det = new DespachoDetalle();
                $det->id_despacho = $id_despacho;
                $det->id_guia = $g['id_guia'];
                $det->despacho_detalle_estado = 1;

                if (!$det->save()) {
                    DB::rollBack();
                    session()->flash('error', 'No se pudo guardar el detalle del despacho.');
                    return;
                }

                // ===== RECURSOS =====
                $recursos = DB::table('guias_recursos')
                    ->select('id_recurso')
                    ->where('id_guia', $g['id_guia'])
                    ->get();

                foreach ($recursos as $r) {
                    $dr = new DespachoDetalleRecurso();
                    $dr->id_despacho = $id_despacho;
                    $dr->id_recurso = $r->id_recurso;
                    $dr->despacho_detalle_recurso_estado = 1;

                    if (!$dr->save()) {
                        DB::rollBack();
                        session()->flash('error', 'No se pudo guardar los recursos del despacho.');
                        return;
                    }
                }

                // ACTUALIZAR guia_estado
                DB::table('guias')
                    ->where('id_guia', $g['id_guia'])
                    ->update(['guia_estado' => 3]);
            }

            DB::commit();

            if ($id_despacho) {
                try {
                    $path = $this->general->imprimir_ticket_os_pdf($id_despacho, 2);
                    $this->dispatch('openPdf', url: asset($path));
                } catch (\Exception $e) {
                    $this->logs->insertarLog($e);
                }
            }

            $this->guias_seleccionadas = [];
            $this->guias_ids_seleccionadas = [];
            $this->transportista_id = '';
            $this->vehiculo_id = '';
            $this->fecha_despacho = now()->format('Y-m-d');
            $this->resetPage();

            session()->flash('success', 'Despacho guardado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            return;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al guardar el despacho. Por favor, inténtelo nuevamente.');
        }
    }
}
