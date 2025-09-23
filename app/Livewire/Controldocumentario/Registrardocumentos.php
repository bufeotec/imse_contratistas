<?php

namespace App\Livewire\Controldocumentario;

use App\Livewire\Intranet\sidebar;
use App\Models\Cliente;
use App\Models\General;
use App\Models\Tipodocumento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Models\Logs;
use App\Models\Guia;
use App\Models\Guiapersonal;
use App\Models\Guiarecurso;
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
    private $guiapersonal;
    private $guiarecurso;
    public function __construct(){
        $this->logs = new Logs();
        $this->cliente = new Cliente();
        $this->general = new General();
        $this->tipodocumento = new Tipodocumento();
        $this->guia = new Guia();
        $this->guiapersonal = new Guiapersonal();
        $this->guiarecurso = new Guiarecurso();
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

    public $listar_personales_activos = [];
    public $personales_seleccionados = [];
    public $personal_seleccionado = '';

    public $listar_recursos_activos = [];
    public $recursos_seleccionados = [];
    public $recurso_seleccionado = '';
    public $id_recurso = '';
    public $listar_info_guia = [];
    public $listar_recursos = [];
    public $listar_personales = [];


    public $buscar_recurso_search = null;
    public $lista_recurso_filtro = array();
    public $abrir_lista_recurso = false;


    public function mount(){
        $this->guia_fecha_emision = now('America/Lima')->startOfMonth()->format('Y-m-d');
        $this->personales_seleccionados = [];
        $this->recursos_seleccionados = [];
    }

    public function render(){
        $listar_guias = $this->guia->listar_guias_registradas($this->search_registrar_documento, $this->pagination_registrar_documento);

        $listar_tipo_doc = $this->tipodocumento->listar_tipo_documento_activos();
        $listar_tipo_doc_busqueda = $this->tipodocumento->listar_tipo_documento_activos();

        // OBTENER PERSONALES ACTIVOS
        $this->listar_personales_activos = DB::table('personales')->where('personal_estado', '=', 1)->get();

        // OBTENER RECURSOS ACTIVOS
        $this->listar_recursos_activos = DB::table('recursos as r')
            ->join('medida as m', 'r.id_medida', '=', 'm.id_medida')
            ->join('tipos_recursos as tr', 'r.id_tipo_recurso', '=', 'tr.id_tipo_recurso')
            ->select(
                'r.*',
                'm.medida_nombre',
                'tr.tipo_recurso_concepto'
            )
            ->where('r.recurso_estado', '=', 1)
            ->get();

        return view('livewire.controldocumentario.registrardocumentos', compact('listar_guias', 'listar_tipo_doc_busqueda', 'listar_tipo_doc'));
    }

    public function clear_form(){
        //GUIA
        $this->id_guia = "";
        $this->guia_serie = "";
        $this->guia_correlativo = "";
        $this->guia_fecha_emision = now('America/Lima')->startOfMonth()->format('Y-m-d');
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

        $this->personales_seleccionados = [];
        $this->recursos_seleccionados = [];
        $this->personal_seleccionado = '';
        $this->recurso_seleccionado = '';

        $this->id_recurso = "";
        $this->buscar_recurso_search = null;
        $this->lista_recurso_filtro = array();
        $this->abrir_lista_recurso = false;
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

    public function buscar_recurso_filtro(){
        try {
            $buscar = $this->buscar_recurso_search ?? '';

            $this->lista_recurso_filtro = DB::table('recursos')
                ->where('recurso_estado','=', 1)
                ->where(function($q) use ($buscar) {
                    $q->where('recurso_nombre', 'like', '%' . $buscar . '%');
                })
                ->limit(10)
                ->get();

            $this->abrir_lista_recurso = true;

        } catch (\Exception $e){
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error. Por favor, inténtelo nuevamente.');
            return;
        }
    }

    public function seleccionar_recurso_vista($id_recurso){
        try {
            $this->abrir_lista_recurso = false;
            $id_c = base64_decode($id_recurso);

            if ($id_c) {
                $data = DB::table('recursos')
                    ->where('id_recurso', '=', $id_c)
                    ->first();

                // Asignar siempre al modal
                $this->buscar_recurso_search = $data->recurso_nombre;
                $this->id_recurso = $id_c;

            } else {
                session()->flash('error', 'Los parámetros del recurso no son válidos.');
                return;
            }
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error. Por favor, inténtelo nuevamente.');
            return;
        }
    }

    public function save_documento(){
        try {
            $this->validate([
                'guia_serie' => 'nullable|string',
                'guia_correlativo' => 'nullable|string',
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
                'cliente_telefono' => 'nullable|string|max:9',
                'cliente_email' => 'nullable|email',
                'id_cliente' => 'nullable|integer',
            ], [
                'guia_serie.string' => 'La serie debe ser texto válido.',

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

                'cliente_telefono.string' => 'El teléfono debe ser texto válido.',
                'cliente_telefono.max' => 'El número de teléfono no debe exceder los 9 caracteres.',

                'cliente_email.email' => 'Debe ingresar un correo electrónico válido.',

                'id_cliente.integer' => 'El ID del cliente debe ser un número entero.',
            ]);

            // Validar que se haya seleccionado al menos un recurso (obligatorio)
            if (empty($this->recursos_seleccionados)) {
                session()->flash('error', 'Debe seleccionar al menos un recurso.');
                return;
            }

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
                    $clienteNew->cliente_telefono = $this->cliente_telefono !== null ? $this->cliente_telefono : $clienteNew->cliente_telefono;
                    $clienteNew->cliente_email = $this->cliente_email !== null ? $this->cliente_email : $clienteNew->cliente_email;
                    $clienteNew->cliente_microtime = $microtime;
                    $clienteNew->cliente_estado = 1;
                    if (!$clienteNew->save()) {
                        DB::rollBack();
                        session()->flash('error', 'Ocurrío un error al crear al cliente');
                        return;
                    }
                }

                $correlativo = $this->guia->obtener_ultimo_correlativo();

                $save_guia = new Guia();
                $save_guia->id_users = Auth::id();
                $save_guia->id_cliente = $cliente_ ? $cliente_->id_cliente : $clienteNew->id_cliente;
                $save_guia->guia_serie = 'GT';
                $save_guia->guia_correlativo = $correlativo;
                $save_guia->guia_fecha_emision = $this->guia_fecha_emision;
                $save_guia->guia_trabajo_realizar = $this->guia_trabajo_realizar;
                $save_guia->guia_modalidad_transporte = $this->guia_modalidad_transporte;
                if ($this->guia_documento) {
                    $save_guia->guia_documento = $this->general->save_files($this->guia_documento, 'documento/guia');
                }
                $save_guia->guia_microtime = $microtime;
                $save_guia->guia_estado = 1;

                if ($save_guia->save()) {
                    // Obtener el ID de la guía recién creada
                    $guia_id = $save_guia->id_guia;

                    // Guardar los personales seleccionados (opcional)
                    if (!empty($this->personales_seleccionados)) {
                        foreach ($this->personales_seleccionados as $personal) {
                            $microtime_gp = microtime(true);

                            $guia_personal = new Guiapersonal();
                            $guia_personal->id_users = Auth::id();
                            $guia_personal->id_guia = $guia_id;
                            $guia_personal->id_personal = $personal['id_personal'];
                            $guia_personal->guia_persona_microtime = $microtime_gp;
                            $guia_personal->guia_persona_estado = 1;
                            if (!$guia_personal->save()) {
                                DB::rollBack();
                                session()->flash('error', 'Ocurrió un error al asignar el personal.');
                                return;
                            }
                        }
                    }

                    // Guardar los recursos seleccionados (obligatorio)
                    foreach ($this->recursos_seleccionados as $recurso) {
                        $microtime_gr = microtime(true);

                        $guia_recurso = new Guiarecurso();
                        $guia_recurso->id_users = Auth::id();
                        $guia_recurso->id_guia = $guia_id;
                        $guia_recurso->id_recurso = $recurso['id_recurso'];
                        $guia_recurso->guia_recurso_cantidad = $recurso['cantidad_solicitada'];
                        $guia_recurso->guia_recurso_microtime = $microtime_gr;
                        $guia_recurso->guia_recurso_estado = 1;
                        if (!$guia_recurso->save()) {
                            DB::rollBack();
                            session()->flash('error', 'Ocurrió un error al asignar los recursos.');
                            return;
                        }
                    }

                    // Limpiar los arrays de selección después de guardar exitosamente
                    $this->personales_seleccionados = [];
                    $this->recursos_seleccionados = [];
                    if (property_exists($this, 'personal_seleccionado')) {
                        $this->personal_seleccionado = '';
                    }
                    if (property_exists($this, 'recurso_seleccionado')) {
                        $this->recurso_seleccionado = '';
                    }

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

    public function btn_info_guia($id){
        $this->id_guia = base64_decode($id);

        $this->listar_info_guia = DB::table('guias as g')
            ->join('clientes as c', 'g.id_cliente', 'c.id_cliente')
            ->where('g.id_guia', '=', $this->id_guia)
            ->where('c.cliente_estado', '=', 1)
            ->where('g.guia_estado', '=', 1)
            ->first();

        // Recursos de la guía
        $this->listar_recursos = DB::table('guias_recursos as gr')
            ->join('recursos as r', 'gr.id_recurso', '=', 'r.id_recurso')
            ->join('tipos_recursos as tr', 'r.id_tipo_recurso', '=', 'tr.id_tipo_recurso')
            ->join('medida as m', 'r.id_medida', '=', 'm.id_medida')
            ->where('gr.id_guia', '=', $this->id_guia)
            ->where('r.recurso_estado', '=', 1)
            ->where('tr.tipo_recurso_estado', '=', 1)
            ->where('gr.guia_recurso_estado', '=', 1)
            ->get();

        // Personales de la guía
        $this->listar_personales = DB::table('guias_personas as gp')
            ->join('personales as p', 'gp.id_personal', 'p.id_personal')
            ->where('gp.id_guia', '=', $this->id_guia)
            ->where('p.personal_estado', '=', 1)
            ->where('gp.guia_persona_estado', '=', 1)
            ->get();
    }

    // PERSONALES
    public function agregar_personal(){
        // Validar que se haya seleccionado un personal
        if (empty($this->personal_seleccionado)) {
            session()->flash('error_eliminar_personal', 'Debe seleccionar un personal.');
            return;
        }

        // Verificar que el personal no esté ya agregado
        if (isset($this->personales_seleccionados[$this->personal_seleccionado])) {
            session()->flash('error_eliminar_personal', 'El personal ya está agregado.');
            return;
        }

        // Buscar la información completa del personal seleccionado
        $personal = DB::table('personales')
            ->where('id_personal', $this->personal_seleccionado)
            ->where('personal_estado', 1)
            ->first();

        if ($personal) {
            // Agregar al array de seleccionados
            $this->personales_seleccionados[$personal->id_personal] = [
                'id_personal' => $personal->id_personal,
                'personal_nombre' => $personal->personal_nombre,
                'personal_apellido' => $personal->personal_apellido,
                'personal_gmail' => $personal->personal_gmail
            ];
            // Limpiar la selección
            $this->personal_seleccionado = '';

            session()->flash('success_agregar_personal', 'Personal agregado correctamente.');
        }
    }

    public function eliminar_personal($id_personal){
        // Verificar que el personal esté en la lista de seleccionados
        if (isset($this->personales_seleccionados[$id_personal])) {
            // Eliminar del array de seleccionados
            unset($this->personales_seleccionados[$id_personal]);

            session()->flash('success_agregar_personal', 'Personal eliminado correctamente.');
        }
    }

    // RECURSOS
    public function agregar_recurso(){
        // Validar que se haya seleccionado un recurso
        if (empty($this->id_recurso)) {
            session()->flash('error_recurso', 'Debe seleccionar un recurso.');
            return;
        }

        // Verificar que el recurso no esté ya agregado
        if (isset($this->recursos_seleccionados[$this->id_recurso])) {
            session()->flash('error_recurso', 'El recurso ya está agregado.');
            return;
        }

        // Buscar la información completa del recurso seleccionado
        $recurso = DB::table('recursos as r')
            ->join('medida as m', 'r.id_medida', '=', 'm.id_medida')
            ->join('tipos_recursos as tr', 'r.id_tipo_recurso', '=', 'tr.id_tipo_recurso')
            ->select(
                'r.*',
                'm.medida_nombre',
                'tr.tipo_recurso_concepto'
            )
            ->where('r.id_recurso', $this->id_recurso)
            ->where('r.recurso_estado', 1)
            ->first();

        if ($recurso) {
            // Agregar al array de seleccionados con cantidad inicial de 1
            $this->recursos_seleccionados[$recurso->id_recurso] = [
                'id_recurso' => $recurso->id_recurso,
                'recurso_nombre' => $recurso->recurso_nombre,
                'recurso_cantidad_maxima' => $recurso->recurso_cantidad,
                'medida_nombre' => $recurso->medida_nombre,
                'tipo_recurso_concepto' => $recurso->tipo_recurso_concepto,
                'cantidad_solicitada' => 1
            ];

            // Limpiar la selección
            $this->id_recurso = '';
            $this->buscar_recurso_search = null;

            session()->flash('success_recurso', 'Recurso agregado correctamente.');
        }
    }

    public function eliminar_recurso($id_recurso){
        // Verificar que el recurso esté en la lista de seleccionados
        if (isset($this->recursos_seleccionados[$id_recurso])) {
            // Eliminar del array de seleccionados
            unset($this->recursos_seleccionados[$id_recurso]);

            session()->flash('success_recurso', 'Recurso eliminado correctamente.');
        }
    }

    public function validar_cantidad($id_recurso, $cantidad_solicitada){
        // Verificar que el recurso existe en los seleccionados
        if (!isset($this->recursos_seleccionados[$id_recurso])) {
            return;
        }

        $recurso = $this->recursos_seleccionados[$id_recurso];
        $cantidad_solicitada = (int) $cantidad_solicitada;

        // Validar que la cantidad sea positiva
        if ($cantidad_solicitada <= 0) {
            $this->recursos_seleccionados[$id_recurso]['cantidad_solicitada'] = 1;
            session()->flash('error_cantidad_' . $id_recurso, 'La cantidad debe ser mayor a 0.');
            return;
        }

        // Validar que no exceda la cantidad máxima
        if ($cantidad_solicitada > $recurso['recurso_cantidad_maxima']) {
            $this->recursos_seleccionados[$id_recurso]['cantidad_solicitada'] = $recurso['recurso_cantidad_maxima'];
            session()->flash('error_cantidad_' . $id_recurso, 'La cantidad no puede exceder ' . $recurso['recurso_cantidad_maxima'] . ' ' . $recurso['medida_nombre']);
            return;
        }

        // Si la validación es exitosa, actualizar la cantidad
        $this->recursos_seleccionados[$id_recurso]['cantidad_solicitada'] = $cantidad_solicitada;

        // Limpiar mensajes de error previos
        session()->forget('error_cantidad_' . $id_recurso);
    }

    // PASAR A MODULO GESTIÓN PROGRAMAR DESPACHO
    public function btn_modal_registrar($id){
        $this->id_guia = base64_decode($id);
    }

    public function guia_pasar_programar_despacho(){
        try {
            $this->validate([
                'id_guia' => 'required|integer',
            ], [
                'id_guia.required' => 'El identificador es obligatorio.',
                'id_guia.integer' => 'El identificador debe ser un número entero.',
            ]);

            DB::beginTransaction();
            $guia = Guia::find($this->id_guia);
            $guia->guia_estado = 2;
            if ($guia->save()) {
                DB::commit();
                $this->dispatch('hide_modal_registrar_personal_recurso');
                session()->flash('success', 'Guía enviada a Programación.');
            } else {
                DB::rollBack();
                session()->flash('error_delete', 'No se pudo cambiar el estado de la guía.');
                return;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error_delete', 'Ocurrió un error. Por favor, inténtelo nuevamente.');
        }
    }

}
