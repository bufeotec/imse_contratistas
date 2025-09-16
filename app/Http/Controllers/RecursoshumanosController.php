<?php

namespace App\Http\Controllers;
use App\Models\Logs;

class RecursoshumanosController extends Controller
{
    private $logs;
    private $cliente;
    public function __construct(){
        $this->logs = new Logs();
    }

    public function personales(){
        try {
            return view('recursos_humanos.personales');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
}
