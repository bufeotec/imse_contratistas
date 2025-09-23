<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    private $logs;
    public function __construct(){
        $this->logs = new Logs();
    }

    public function historial_guias(){
        try {
            return view('reportes.historial_guias');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
}
