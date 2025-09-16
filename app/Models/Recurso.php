<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Recurso extends Model{
    use HasFactory;
    protected $table = "recursos";
    protected $primaryKey = "id_recurso";
    private $logs;

    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_recursos_activos($search,$pagination,$order = 'asc'){
        try {
            $query = DB::table('recursos as r')
                ->join('tipos_recursos as tr', 'r.id_tipo_recurso', 'tr.id_tipo_recurso')
                ->join('medida as m', 'r.id_medida', 'm.id_medida')
                ->where(function($q) use ($search) {
                    $q->where('r.recurso_cantidad', 'like', '%' . $search . '%')
                        ->orWhereNull('r.recurso_cantidad');
                })->orderBy('r.id_recurso', $order);

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
}
