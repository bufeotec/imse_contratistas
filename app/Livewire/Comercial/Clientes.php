<?php

namespace App\Livewire\Comercial;

use App\Livewire\Intranet\sidebar;
use App\Models\General;
use App\Models\Menu;
use http\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Models\Logs;
use App\Models\Cliente;
use App\Models\Tipodocumento;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Clientes extends Component{
    use WithPagination, WithoutUrlPagination;
    private $logs;
    private $cliente;
    private $general;
    private $tipodocumento;
    private $tipocliente;
    public function __construct(){
        $this->logs = new Logs();
        $this->cliente = new Cliente();
        $this->general = new General();
        $this->tipodocumento = new Tipodocumento();
    }
    public $search_cliente;
    public $pagination_cliente = 10;
    public $id_cliente = "";
    public $id_tipo_documento = "";
    public $id_tipo_cliente = "";
    public $cliente_nombre = "";
    public $cliente_numero_documento = "";
    public $cliente_direccion = "";
    public $cliente_telefono = "";
    public $cliente_email = "";
    public $cliente_estado = "";
    public $messageConsulta = '';
    public $cliente_razon_social = '';
    public $cliente_nombre_comercial = '';
    public $cliente_persona_contacto = '';
    public $cliente_numero_contacto = '';
    public $cliente_observacion = '';
    public $mostrar = true;
    public $id_dupli;
    public $info_cliente = null;
    public $id_tipo_documento_busqueda = "";
    public $id_tipo_cliente_busqueda = "";
    public $fecha_inicio;
    public $fecha_fin;

    public function mount()
    {
        if (!$this->fecha_inicio) {
            $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->fecha_fin) {
            $this->fecha_fin = now()->format('Y-m-d');
        }
    }

    public function render(){
        $listar_cliente = $this->cliente->listar_clientes($this->id_tipo_documento_busqueda, $this->id_tipo_cliente_busqueda, $this->search_cliente, $this->pagination_cliente);
        $listar_tipo_doc = $this->tipodocumento->listar_tipo_documento_activos();


        $listar_tipo_doc_busqueda = $this->tipodocumento->listar_tipo_documento_activos();
        return view('livewire.comercial.clientes', compact('listar_cliente', 'listar_tipo_doc', 'listar_tipo_doc_busqueda'));
    }

    public function modal_informacion_cliente($id) {
        try {
            $id_decoded = base64_decode($id);
            $this->info_cliente = $this->cliente->listar_info_cliente_x_id($id_decoded);

            if (!$this->info_cliente) {
                session()->flash('error', 'Cliente no encontrado.');
                $this->info_cliente = null;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar la información.');
            $this->info_cliente = null;
        }
    }

    public function clear_form(){
        $this->id_cliente = "";
        $this->id_tipo_documento = "";
        $this->id_tipo_cliente = "";
        $this->cliente_numero_documento = "";
        $this->cliente_direccion = "";
        $this->cliente_telefono = "";
        $this->cliente_email = "";
        $this->cliente_razon_social = "";
        $this->cliente_nombre_comercial = "";
        $this->cliente_persona_contacto = "";
        $this->cliente_numero_contacto = "";
        $this->cliente_observacion = "";
        $this->cliente_estado = "";
        $this->messageConsulta = "";
    }

    public function consultarDocumento(){
        try {
            $this->mostrar = true;
            $this->id_dupli = "";
            $this->messageConsulta = "";
            $this->cliente_razon_social = "";
            $this->cliente_direccion = "";
            $this->cliente_razon_social = "";
            $this->cliente_nombre_comercial = "";
            $consultarCliente = $this->cliente->listar_cliente_x_documento($this->cliente_numero_documento);
            if(!$consultarCliente){
                $resultado = $this->general->consultar_documento($this->id_tipo_documento,$this->cliente_numero_documento);
                if ($resultado['result']['tipo'] == 'success'){
                    $this->cliente_razon_social = $resultado['result']['name'];
                    $this->cliente_direccion = $resultado['result']['direccion'];
                }
                $this->messageConsulta = array('mensaje'=>$resultado['result']['mensaje'],'type'=>$resultado['result']['tipo']);
            }else{
                $this->mostrar = false;
                $this->cliente_razon_social = $consultarCliente->cliente_razon_social;
                $this->id_dupli = $consultarCliente->id_cliente;
                $this->id_tipo_documento = $consultarCliente->id_tipo_documento;
                $this->cliente_nombre_comercial = $consultarCliente->cliente_nombre_comercial;
            }

        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al guardar el registro. Por favor, inténtelo nuevamente.');
            return;
        }
    }

    public function save_cliente(){
        try {
            $this->validate([
                'id_tipo_documento' => 'required|integer',
                'cliente_nombre_comercial' => 'required|string',
                'cliente_razon_social' => 'required|string',
                'cliente_numero_documento' => 'required|numeric',
                'cliente_direccion' => 'required|string',
                'cliente_telefono' => 'required|string|max:9',
                'cliente_email' => 'nullable|email',
                'cliente_persona_contacto' => 'nullable|string',
                'cliente_numero_contacto' => 'nullable|string|max:9',
                'cliente_observacion' => 'nullable|string',
                'id_cliente' => 'nullable|integer',
            ], [
                'id_tipo_documento.required' => 'El ID del tipo documento es obligatorio.',
                'id_tipo_documento.integer' => 'El ID del tipo documento debe ser un número entero.',

                'cliente_nombre_comercial.required' => 'El nombre comercial es obligatorio.',
                'cliente_nombre_comercial.string' => 'El nombre comercial debe ser texto válido.',

                'cliente_razon_social.required' => 'La razón social es obligatorio.',
                'cliente_razon_social.string' => 'La razón social debe ser texto válido.',

                'cliente_numero_documento.required' => 'El número de documento (DNI/RUC) es obligatorio.',
                'cliente_numero_documento.numeric' => 'El documento debe ser numérico.',

                'cliente_direccion.required' => 'La dirección del cliente es obligatoria.',
                'cliente_direccion.string' => 'La dirección debe ser texto válido.',

                'cliente_telefono.required' => 'El teléfono del cliente es obligatorio.',
                'cliente_telefono.string' => 'El teléfono debe ser texto válido.',
                'cliente_telefono.max' => 'El número de teléfono no debe exceder los 9 caracteres.',

//                'cliente_email.required' => 'El correo electrónico es obligatorio.',
                'cliente_email.email' => 'Debe ingresar un correo electrónico válido.',

                'cliente_persona_contacto.string' => 'El nombre del personal de contacto debe ser una cadena de texto.',

                'cliente_numero_contacto.string' => 'El número del personal de contacto debe ser texto válido.',
                'cliente_numero_contacto.max' => 'El número del personal de contacto no debe exceder los 9 caracteres.',

                'cliente_observacion.string' => 'La observación debe ser una cadena de texto.',

                'id_cliente.integer' => 'El ID del cliente debe ser un número entero.',
            ]);

            if (!$this->id_cliente){ // INSERT
                if (!Gate::allows('crear_cliente')) {
                    session()->flash('error', 'No tiene permisos para crear.');
                    return;
                }

                $validar = DB::table('clientes')->where('cliente_numero_documento', '=',$this->cliente_numero_documento)->exists();
                if ($validar) {
                    session()->flash('error', 'Ya existe un cliente registrado con este documento.');
                    return;
                }

                $microtime = microtime(true);

                DB::beginTransaction();
                $save_cliente = new Cliente();
                $save_cliente->id_users = Auth::id();
                $save_cliente->id_tipo_documento = $this->id_tipo_documento;
                $save_cliente->cliente_numero_documento = $this->cliente_numero_documento;
                $save_cliente->cliente_razon_social = $this->cliente_razon_social;
                $save_cliente->cliente_nombre_comercial = $this->cliente_nombre_comercial;
                $save_cliente->cliente_direccion = $this->cliente_direccion;
                $save_cliente->cliente_telefono = $this->cliente_telefono;
                $save_cliente->cliente_email = !empty($this->cliente_email) ? $this->cliente_email : null;
                $save_cliente->cliente_persona_contacto = !empty($this->cliente_persona_contacto) ? $this->cliente_persona_contacto : null;
                $save_cliente->cliente_numero_contacto = !empty($this->cliente_numero_contacto) ? $this->cliente_numero_contacto : null;
                $save_cliente->cliente_observacion = !empty($this->cliente_observacion) ? $this->cliente_observacion : null;
                $save_cliente->cliente_microtime = $microtime;
                $save_cliente->cliente_estado = 1;

                if ($save_cliente->save()) {

                    DB::commit();
                    $this->dispatch('hideModal');
                    session()->flash('success', 'Registro guardado correctamente.');
                } else {
                    DB::rollBack();
                    session()->flash('error', 'Ocurrió un error al guardar el cliente.');
                    return;
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al guardar el registro. Por favor, inténtelo nuevamente.');
        }
    }

    public function editar($id)
    {
        try {
            $id_decoded = base64_decode($id);
            $c = \App\Models\Cliente::find($id_decoded);

            if (!$c) {
                session()->flash('error', 'Cliente no encontrado.');
                return;
            }

            // Habilita edición completa
            $this->mostrar = true;
            $this->messageConsulta = "";

            // Setea datos al formulario
            $this->id_cliente               = $c->id_cliente;
            $this->id_tipo_documento        = $c->id_tipo_documento;
            $this->cliente_numero_documento = $c->cliente_numero_documento;
            $this->cliente_razon_social     = $c->cliente_razon_social;
            $this->cliente_nombre_comercial = $c->cliente_nombre_comercial;
            $this->cliente_direccion        = $c->cliente_direccion;
            $this->cliente_telefono         = $c->cliente_telefono;
            $this->cliente_email            = $c->cliente_email;
            $this->cliente_persona_contacto = $c->cliente_persona_contacto;
            $this->cliente_numero_contacto  = $c->cliente_numero_contacto;
            $this->cliente_observacion      = $c->cliente_observacion;

        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al cargar el cliente.');
        }
    }
}
