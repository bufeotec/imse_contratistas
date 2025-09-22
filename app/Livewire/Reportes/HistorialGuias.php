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
}
