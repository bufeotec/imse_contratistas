<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Vehiculo extends Model{
    use HasFactory;
    protected $table = "vehiculos";
    protected $primaryKey = "id_vehiculo";
    private $logs;

    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_vehiculo_activos($search,$pagination,$order = 'asc'){
        try {

            $query = DB::table('vehiculos')
                ->where(function($q) use ($search) {
                    $q->where('vehiculo_placa', 'like', '%' . $search . '%')
                        ->orWhereNull('vehiculo_placa');
                })->orderBy('id_vehiculo', $order);

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
