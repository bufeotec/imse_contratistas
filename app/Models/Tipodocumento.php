<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tipodocumento extends Model
{
    use HasFactory;
    protected $table = "tipos_documentos";
    protected $primaryKey = "id_tipo_documento";
    private $logs;

    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function listar_tipo_documento_activos(){
        try {
            $result = DB::table('tipos_documentos')
                ->where('tipo_documento_estado','=',1)
                ->get();
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            $result = [];
        }
        return $result;
    }
}
