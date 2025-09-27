<?php

namespace App\Livewire\Reportes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Logs;

class HistorialGuias extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';

    private $logs;

    // filtros
    public $search = '';
    public $fecha_desde = null;
    public $fecha_hasta = null;
    public $pagination_guias = 10;

    public $listar_info_guia = [];
    public $listar_recursos = [];
    public $listar_personales = [];

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
            $query = DB::table('guias as g')
                ->join('clientes as c', 'c.id_cliente', '=', 'g.id_cliente')
                ->select(
                    'g.id_guia',
                    'g.guia_serie',
                    'g.guia_correlativo',
                    'g.guia_fecha_emision',
                    'g.guia_documento',
                    'g.guia_estado',
                    'c.cliente_razon_social'
                )
                ->whereIn('g.guia_estado', [1, 2, 3]);

            // buscador
            $s = trim($this->search);
            if ($s !== '') {
                $query->where(function ($w) use ($s) {
                    $w->where('c.cliente_razon_social', 'like', "%{$s}%")
                        ->orWhere('g.guia_serie', 'like', "%{$s}%")
                        ->orWhere('g.guia_correlativo', 'like', "%{$s}%")
                        ->orWhereRaw("CONCAT(g.guia_serie,'-',g.guia_correlativo) LIKE ?", ["%{$s}%"]);
                });
            }

            // fechas
            if (!empty($this->fecha_desde)) {
                $query->whereDate('g.guia_fecha_emision', '>=', $this->fecha_desde);
            }
            if (!empty($this->fecha_hasta)) {
                $query->whereDate('g.guia_fecha_emision', '<=', $this->fecha_hasta);
            }

            $guias = $query
                ->orderByDesc('g.guia_fecha_emision')
                ->orderByDesc('g.id_guia')
                ->paginate($this->pagination_guias);

            return view('livewire.reportes.historial-guias', compact('guias'));
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            $guias = new LengthAwarePaginator([], 0, $this->pagination_guias);
            return view('livewire.reportes.historial-guias', compact('guias'));
        }
    }

    public function btn_info_guia($id_guia){

        $id = base64_decode($id_guia);

        $this->listar_info_guia = DB::table('guias as g')
            ->join('clientes as c', 'g.id_cliente', 'c.id_cliente')
            ->where('g.id_guia', '=', $id)
            ->where('c.cliente_estado', '=', 1)
            ->first();

        // Recursos de la guía
        $this->listar_recursos = DB::table('guias_recursos as gr')
            ->join('recursos as r', 'gr.id_recurso', '=', 'r.id_recurso')
            ->join('tipos_recursos as tr', 'r.id_tipo_recurso', '=', 'tr.id_tipo_recurso')
            ->join('medida as m', 'r.id_medida', '=', 'm.id_medida')
            ->where('gr.id_guia', '=', $id)
            ->where('r.recurso_estado', '=', 1)
            ->where('tr.tipo_recurso_estado', '=', 1)
            ->where('gr.guia_recurso_estado', '=', 1)
            ->get();

        // Personales de la guía
        $this->listar_personales = DB::table('guias_personas as gp')
            ->join('personales as p', 'gp.id_personal', 'p.id_personal')
            ->where('gp.id_guia', '=', $id)
            ->where('p.personal_estado', '=', 1)
            ->where('gp.guia_persona_estado', '=', 1)
            ->get();
    }
}
