<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfigurationController extends Controller
{
    private $logs;
    private $menu;
    public function __construct()
    {
        $this->logs = new Logs();
        $this->menu = new Menu();
    }

    public function menus(){
        try {


            return view('configuration.menu');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

    public function submenu(){
        try {
            $id_menu = base64_decode($_GET['data']);
            if ($id_menu){
                $informacion_menu = $this->menu->listar_menu_x_id($id_menu);

                return view('configuration.submenu',compact('informacion_menu'));
            }else{
                return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
            }
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

    public function usuarios(){
        try {


            return view('configuration.usuarios');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
    public function roles(){
        try {


            return view('configuration.roles');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
    public function iconos(){
        try {


            return view('configuration.icono');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }
    public function empresas(){
        try {


            return view('configuration.empresas');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

}
