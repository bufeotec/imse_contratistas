<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;

class DespachosController extends Controller
{
    private $logs;
    public function __construct(){
        $this->logs = new Logs();
    }

    public function gestionar_despachos(){
        try {
            return view('despacho_transporte.gestionar_despachos');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
}
