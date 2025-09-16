<?php

namespace App\Livewire\Controldocumentario;

use App\Models\Cliente;
use App\Models\General;
use App\Models\Tipodocumento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Models\Logs;
use App\Models\Guia;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Registrardocumentos extends Component{
    use WithPagination, WithoutUrlPagination;
    use WithFileUploads;
    private $logs;
    private $cliente;
    private $general;
    private $tipodocumento;
    private $guia;
    public function __construct(){
        $this->logs = new Logs();
        $this->cliente = new Cliente();
        $this->general = new General();
        $this->tipodocumento = new Tipodocumento();
        $this->guia = new Guia();
    }
    public $search_registrar_documento;
    public $pagination_registrar_documento = 10;
    public $id_guia = "";
    public $id_cliente = "";
    public $guia_serie = "";
    public $guia_correlativo = "";
    public $guia_fecha_emision = "";
    public $guia_trabajo_realizar = "";
    public $guia_modalidad_transporte = "";
    public $guia_documento = "";
    public $guia_estado = "";

    // CLIENTE
    public $id_tipo_documento = "";
    public $cliente_numero_documento = "";
    public $cliente_direccion = "";
    public $cliente_telefono = "";
    public $cliente_email = "";
    public $cliente_estado = "";
    public $messageConsulta = '';
    public $cliente_razon_social = '';
    public $cliente_nombre_comercial = '';
    public $id_tipo_documento_busqueda = "";
    public $id_tipo_cliente_busqueda = "";
    public $mostrar = true;
    public function mount(){
        $this->guia_fecha_emision = now('America/Lima')->startOfMonth()->format('Y-m-d');
    }

    public function render(){
        $listar_guias = $this->guia->listar_guias_registradas($this->search_registrar_documento, $this->pagination_registrar_documento);

        $listar_tipo_doc = $this->tipodocumento->listar_tipo_documento_activos();
        $listar_tipo_doc_busqueda = $this->tipodocumento->listar_tipo_documento_activos();
        return view('livewire.controldocumentario.registrardocumentos', compact('listar_guias', 'listar_tipo_doc_busqueda', 'listar_tipo_doc'));
    }

    public function clear_form(){
        //GUIA
        $this->id_guia = "";
        $this->guia_serie = "";
        $this->guia_correlativo = "";
        $this->guia_fecha_emision = "";
        $this->guia_trabajo_realizar = "";
        $this->guia_modalidad_transporte = "";
        $this->guia_documento = "";
        $this->guia_estado = "";
        // CLIENTE
        $this->id_cliente = "";
        $this->id_tipo_documento = "";
        $this->id_tipo_cliente = "";
        $this->cliente_numero_documento = "";
        $this->cliente_direccion = "";
        $this->cliente_telefono = "";
        $this->cliente_email = "";
        $this->cliente_razon_social = "";
        $this->cliente_nombre_comercial = "";
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

    public function save_documento(){
        try {
            $this->validate([
                'guia_serie' => 'required|string',
                'guia_correlativo' => 'required|string',
                'guia_fecha_emision' => 'required|date',
                'guia_trabajo_realizar' => 'required|string',
                'guia_modalidad_transporte' => 'required|integer',
                'guia_documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'id_guia' => 'nullable|integer',

                'id_tipo_documento' => 'required|integer',
                'cliente_nombre_comercial' => 'required|string',
                'cliente_razon_social' => 'required|string',
                'cliente_numero_documento' => 'required|numeric',
                'cliente_direccion' => 'required|string',
                'cliente_telefono' => 'required|string|max:9',
                'cliente_email' => 'nullable|email',
                'id_cliente' => 'nullable|integer',
            ], [
                'guia_serie.required' => 'La serie es obligatorio.',
                'guia_serie.string' => 'La serie debe ser texto válido.',

                'guia_correlativo.required' => 'El correlativo es obligatorio.',
                'guia_correlativo.string' => 'El correlativo debe ser texto válido.',

                'guia_fecha_emision.required' => 'La fecha de emisión es obligatorio.',
                'guia_fecha_emision.date' => 'La fecha de emisión debe ser una fecha valida.',

                'guia_trabajo_realizar.required' => 'El trabajo a realizar es obligatorio.',
                'guia_trabajo_realizar.string' => 'El trabajo a realizar debe ser texto válido.',

                'guia_modalidad_transporte.required' => 'La modalidad de transporte es obligatorio.',
                'guia_modalidad_transporte.string' => 'La modalidad de transporte debe ser un valor numérico.',

                'guia_documento.file' => 'Debe cargar un archivo válido.',
                'guia_documento.mimes' => 'El archivo debe ser una imagen en formato JPG, JPEG o PNG.',
                'guia_documento.max' => 'La imagen no puede exceder los 2MB.',

                'id_guia.integer' => 'El ID del cliente debe ser un número entero.',

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

                'cliente_email.email' => 'Debe ingresar un correo electrónico válido.',

                'id_cliente.integer' => 'El ID del cliente debe ser un número entero.',
            ]);

            if (!$this->id_guia){
                if (!Gate::allows('crear_guia')) {
                    session()->flash('error', 'No tiene permisos para crear.');
                    return;
                }

                DB::beginTransaction();
                $microtime = microtime(true);
                $numero_cliente = $this->cliente_numero_documento;
                $cliente_ = $this->cliente->buscar_cliente_numero($numero_cliente);

                if (!$cliente_) {
                    $clienteNew = new Cliente();
                    $clienteNew->id_users = Auth::id();
                    $clienteNew->id_tipo_documento = $this->id_tipo_documento;
                    $clienteNew->cliente_razon_social = $this->cliente_razon_social;
                    $clienteNew->cliente_nombre_comercial = $this->cliente_nombre_comercial;
                    $clienteNew->cliente_numero_documento = $numero_cliente;
                    $clienteNew->cliente_direccion = $this->cliente_direccion;
                    $clienteNew->cliente_telefono = $this->cliente_telefono;
                    $clienteNew->cliente_microtime = $microtime;
                    $clienteNew->cliente_estado = 1;
                    if (!$clienteNew->save()) {
                        DB::rollBack();
                        session()->flash('error', 'Ocurrío un error al crear al cliente');
                        return;
                    }
                }

                $save_guia = new Guia();
                $save_guia->id_users = Auth::id();
                $save_guia->id_cliente = $cliente_ ? $cliente_->id_cliente : $clienteNew->id_cliente;
                $save_guia->guia_serie = $this->guia_serie;
                $save_guia->guia_correlativo = $this->guia_correlativo;
                $save_guia->guia_fecha_emision = $this->guia_fecha_emision;
                $save_guia->guia_trabajo_realizar = $this->guia_trabajo_realizar;
                $save_guia->guia_modalidad_transporte = $this->guia_modalidad_transporte;
                if ($this->guia_documento) {
                    $save_guia->guia_documento = $this->general->save_files($this->guia_documento, 'documento/guia');
                }
                $save_guia->guia_microtime = $microtime;
                $save_guia->guia_estado = 1;

                if ($save_guia->save()) {
                    DB::commit();
                    $this->dispatch('hide_modal_registrar_documento');
                    session()->flash('success', 'Registro guardado correctamente.');
                } else {
                    DB::rollBack();
                    session()->flash('error', 'Ocurrió un error al guardar.');
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

    public function save_personal_recurso(){
        try {

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al guardar el registro. Por favor, inténtelo nuevamente.');
        }
    }

}
