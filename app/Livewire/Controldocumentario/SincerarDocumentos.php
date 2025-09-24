<?php

namespace App\Livewire\Controldocumentario;

use App\Models\GuiaSincerada;
use App\Models\GuiaSinceradaDetalle;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;

class SincerarDocumentos extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';

    private $logs;

    // filtros
    public $search = '';
    public $fecha_desde = null;
    public $fecha_hasta = null;
    public $pagination_guias = 10;

    public $expanded = [];
    public $recursosPorGuia = [];
    public $seleccionados = [];

    public function __construct()
    {
        $this->logs = new Logs();
    }

    public function mount()
    {
        $hoy = now()->format('Y-m-d');
        $this->fecha_desde = $hoy;
        $this->fecha_hasta = $hoy;
    }

    public function render()
    {
        try {
            $q = DB::table('guias as g')
                ->join('clientes as c', 'c.id_cliente', '=', 'g.id_cliente')
                ->select(
                    'g.id_guia',
                    'g.guia_serie',
                    'g.guia_correlativo',
                    'g.guia_fecha_emision',
                    'g.guia_trabajo_realizar',
                    'c.cliente_razon_social'
                )
                ->where('g.guia_estado', 3);

            // buscador
            $s = trim($this->search);
            if ($s !== '') {
                $q->where(function($w) use ($s) {
                    $w->where('c.cliente_razon_social', 'like', "%{$s}%")
                        ->orWhere('g.guia_trabajo_realizar', 'like', "%{$s}%")
                        ->orWhere('g.guia_serie', 'like', "%{$s}%")
                        ->orWhere('g.guia_correlativo', 'like', "%{$s}%")
                        ->orWhereRaw("CONCAT(g.guia_serie,'-',g.guia_correlativo) LIKE ?", ["%{$s}%"]);
                });
            }

            // filtro fechas
            if (!empty($this->fecha_desde)) {
                $q->whereDate('g.guia_fecha_emision', '>=', $this->fecha_desde);
            }
            if (!empty($this->fecha_hasta)) {
                $q->whereDate('g.guia_fecha_emision', '<=', $this->fecha_hasta);
            }

            $guias = $q->orderByDesc('g.guia_fecha_emision')
                ->orderByDesc('g.id_guia')
                ->paginate($this->pagination_guias);

            return view('livewire.controldocumentario.sincerar-documentos', compact('guias'));
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            $guias = new LengthAwarePaginator([], 0, $this->pagination_guias);
            return view('livewire.controldocumentario.sincerar-documentos', compact('guias'));
        }
    }

    public function toggleExpand($id_b64)
    {
        try {
            $id = (int) base64_decode($id_b64);
            $this->expanded[$id] = !($this->expanded[$id] ?? false);

            if ($this->expanded[$id] && empty($this->recursosPorGuia[$id])) {
                $recursos = DB::table('guias_recursos as gr')
                    ->join('recursos as r', 'r.id_recurso', '=', 'gr.id_recurso')
                    ->select('r.id_recurso', 'r.recurso_nombre')
                    ->where('gr.id_guia', $id)
                    ->get();

                $this->recursosPorGuia[$id] = $recursos->map(fn($x) => [
                    'id_recurso' => (int)$x->id_recurso,
                    'recurso_nombre' => $x->recurso_nombre,
                ])->toArray();

                if (!isset($this->seleccionados[$id]) || !is_array($this->seleccionados[$id])) {
                    $this->seleccionados[$id] = [];
                }
            }
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
        }
    }

    public function sincerar()
    {
        try {
            $detalle = [];
            foreach ($this->seleccionados as $id_guia => $arrRecursos) {
                if (is_array($arrRecursos) && count($arrRecursos) > 0) {
                    foreach ($arrRecursos as $id_recurso) {
                        $id_guia = (int)$id_guia;
                        $id_recurso = (int)$id_recurso;
                        if ($id_guia > 0 && $id_recurso > 0) {
                            $detalle[] = ['id_guia' => $id_guia, 'id_recurso' => $id_recurso];
                        }
                    }
                }
            }

            if (count($detalle) === 0) {
                session()->flash('error', 'Debe seleccionar al menos un recurso.');
                return;
            }

            DB::beginTransaction();

            $serie = 'GTR';
            $ultimo = DB::table('guia_sincerada')
                ->where('guia_sincerada_serie', $serie)
                ->max('guia_sincerada_correlativo');
            $nuevoCorrelativo = ((int)$ultimo) + 1;

            $userId = Auth::id();

            // guia sincerada
            $sin = new GuiaSincerada();
            $sin->id_users = $userId;
            $sin->guia_sincerada_serie = $serie;
            $sin->guia_sincerada_correlativo = $nuevoCorrelativo;
            $sin->guia_sincerada_fecha = now();
            $sin->guia_sincerada_estado = 1;

            if (!$sin->save()) {
                DB::rollBack();
                session()->flash('error', 'Ocurrió un error al guardar la guia sincerada.');
                return;
            }

            $id_sincerada = $sin->id_guia_sincerada;

            // detalles
            foreach ($detalle as $d) {

                $det = new GuiaSinceradaDetalle();
                $det->id_guia_sincerada = $id_sincerada;
                $det->id_guia = $d['id_guia'];
                $det->id_recurso = $d['id_recurso'];
                $det->guia_sincerada_detalle_cantidad = 0;

                if (!$det->save()) {
                    DB::rollBack();
                    session()->flash('error', 'No se pudo guardar el detalle de la guia sincerada.');
                    return;
                }
            }

            // actualizar estado de las guías involucradas
            $guiaIds = array_values(array_unique(array_column($detalle, 'id_guia')));
            DB::table('guias')
                ->whereIn('id_guia', $guiaIds)
                ->update(['guia_estado' => 4]);

            DB::commit();

            $this->expanded = [];
            $this->recursosPorGuia = [];
            $this->seleccionados = [];
            $this->resetPage();

            $num = str_pad((string)$nuevoCorrelativo, 6, '0', STR_PAD_LEFT);
            session()->flash('success', "Documentos sincerados: {$serie}-{$num}.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al sincerar los documentos. Inténtelo nuevamente.');
        }
    }
}

