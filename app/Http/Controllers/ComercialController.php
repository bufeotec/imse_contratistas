<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Logs;
use Illuminate\Http\Request;

class ComercialController extends Controller
{
    private $logs;
    private $cliente;
    public function __construct(){
        $this->logs = new Logs();
        $this->cliente = new Cliente();
    }

    public function clientes(){
        try {
            return view('comercial.clientes');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
}
