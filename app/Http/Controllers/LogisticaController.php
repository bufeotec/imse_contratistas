<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogisticaController extends Controller{
    private $logs;
    public function __construct(){
        $this->logs = new Logs();
    }

    public function transportistas(){
        try {

            return view('logistica.transportistas');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

    public function vehiculos(){
        try {

            return view('logistica.vehiculos');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

    public function recursos(){
        try {

            return view('logistica.recursos');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
}
