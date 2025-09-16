<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Medida extends Model{
    use HasFactory;
    protected $table = "medida";
    protected $primaryKey = "id_medida";
    private $logs;

    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_medidas(){
        try {
            $result = DB::table('medida')
                ->where('medida_activo', '=', 1)
                ->get();

        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            $result = [];
        }
        return $result;
    }
}
