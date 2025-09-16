<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Transportista extends Model{
    use HasFactory;
    protected $table = "transportistas";
    protected $primaryKey = "id_transportista";
    private $logs;

    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_transportista_activos($search,$pagination,$order = 'asc'){
        try {
            $query = DB::table('transportistas')
                ->where(function($q) use ($search) {
                    $q->where('transportista_ruc', 'like', '%' . $search . '%')
                        ->orWhere('transportista_razon_social', 'like', '%' . $search . '%')
                        ->orWhere('transportista_nom_comercial', 'like', '%' . $search . '%')
                        ->orWhere('transportista_direccion', 'like', '%' . $search . '%')
                        ->orWhereNull('transportista_ruc')
                        ->orWhereNull('transportista_razon_social')
                        ->orWhereNull('transportista_nom_comercial')
                        ->orWhereNull('transportista_direccion');
                })->orderBy('id_transportista', $order);

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

    public function listar_trasnportistas_activos(){
        try {
            $result = DB::table('transportistas')
                ->where('transportista_estado', '=', 1)
                ->get();

        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            $result = [];
        }
        return $result;
    }
}
