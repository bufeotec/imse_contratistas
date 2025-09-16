<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tiporecurso extends Model{
    use HasFactory;
    protected $table = "tipos_recursos";
    protected $primaryKey = "id_tipo_recurso";
    private $logs;

    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_tipos_recursos(){
        try {
            $result = DB::table('tipos_recursos')
                ->where('tipo_recurso_estado', '=', 1)
                ->get();

        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            $result = [];
        }
        return $result;
    }
}
