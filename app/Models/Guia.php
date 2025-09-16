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
}
