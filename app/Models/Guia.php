<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Guia extends Model{
    use HasFactory;
    protected $table = "guias";
    protected $primaryKey = "id_guia";

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_guias_registradas($search,$pagination,$order = 'asc'){
        try {
            $query = DB::table('guias as g')
                ->join('clientes as c', 'g.id_cliente', 'c.id_cliente')
                ->where('g.guia_estado', '=', 1)
                ->where(function($q) use ($search) {
                    $q->where('g.guia_serie', 'like', '%' . $search . '%')
                        ->orWhereNull('g.guia_serie');
                })->orderBy('g.id_guia', $order);

            $result = $query->paginate($pagination);
            // Numeración de filas
            $result->getCollection()->transform(function($item, $key) use ($result) {
                $item->numero = $key + 1 + ($result->currentPage() - 1) * $result->perPage();
                return $item;
            });
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return new LengthAwarePaginator(collect([]), 0, $pagination);
        }
        return $result;
    }

    public function obtener_ultimo_correlativo(){
        try {
            $ultimaGuia = DB::table('guias')
                ->whereNotNull('guia_correlativo')
                ->orderBy('guia_correlativo', 'desc')
                ->first();

            if ($ultimaGuia && !empty($ultimaGuia->guia_correlativo)) {
                // Extraer solo la parte numérica del correlativo (removiendo los ceros a la izquierda)
                $ultimoCorrelativo = (int) $ultimaGuia->guia_correlativo;

                // Incrementar el correlativo y formatear con 4 dígitos
                $nuevoCorrelativo = str_pad($ultimoCorrelativo + 1, 4, '0', STR_PAD_LEFT);
            } else {
                // No hay registros previos: iniciar con el primer correlativo
                $nuevoCorrelativo = "0001";
            }

            return $nuevoCorrelativo;

        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            return "0001"; // Valor por defecto en caso de error
        }
    }

    public function obtener_guia_por_id($id){
        try {
            $result = DB::table('guias as g')
                ->join('clientes as c', 'g.id_cliente', 'c.id_cliente')
                ->where('g.id_guia','=',$id)
                ->first();

        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            $result = [];
        }
        return $result;
    }

    public function obtener_informacion_recurso($id){
        try {
            $result = DB::table('guias_recursos as gr')
                ->join('recursos as r', 'gr.id_recurso', '=', 'r.id_recurso')
                ->join('tipos_recursos as tr', 'r.id_tipo_recurso', '=', 'tr.id_tipo_recurso')
                ->join('medida as m', 'r.id_medida', '=', 'm.id_medida')
                ->where('gr.id_guia', '=', $id)
                ->where('r.recurso_estado', '=', 1)
                ->where('tr.tipo_recurso_estado', '=', 1)
                ->where('gr.guia_recurso_estado', '=', 1)
                ->get();

        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            $result = [];
        }
        return $result;
    }
}
