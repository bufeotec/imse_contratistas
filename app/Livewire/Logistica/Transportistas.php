<?php

namespace App\Livewire\Logistica;

use App\Models\General;
use App\Models\Logs;
use App\Models\Transportista;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Transportistas extends Component{
    use WithPagination, WithoutUrlPagination;
    private $logs;
    private $transportista;
    private $general;
    public function __construct(){
        $this->logs = new Logs();
        $this->transportista = new Transportista();
        $this->general = new General();
    }
    public $search_transportistas;
    public $pagination_transportistas = 10;
    public $id_transportista = "";
    public $transportista_ruc = "";
    public $transportista_razon_social = "";
    public $transportista_nom_comercial = "";
    public $transportista_direccion = "";
    public $transportista_telefono = "";
    public $transportista_gmail = "";
    public $transportista_estado = "";
    public $messageConsulta = "";
    public $message_delete_transportista = "";

    public function render(){
        $listar_transportista = $this->transportista->listar_transportista_activos($this->search_transportistas, $this->pagination_transportistas);
        return view('livewire.logistica.transportistas', compact('listar_transportista'));
    }

    public function clear_form_transportistas(){
        $this->id_transportista = "";
        $this->transportista_ruc = "";
        $this->transportista_razon_social = "";
        $this->transportista_nom_comercial = "";
        $this->transportista_direccion = "";
        $this->transportista_telefono = "";
        $this->transportista_gmail = "";
        $this->transportista_estado = "";
    }

    public function consultDocument(){
        try {
            $this->messageConsulta = "";
            $this->transportista_razon_social = "";
            $this->transportista_direccion = "";
            $resultado = $this->general->consultar_documento(4,$this->transportista_ruc);
            if ($resultado['result']['tipo'] == 'success'){
                $this->transportista_razon_social = $resultado['result']['name'];
                $this->transportista_direccion = $resultado['result']['direccion'];
            }
            $this->messageConsulta = array('mensaje'=>$resultado['result']['mensaje'],'type'=>$resultado['result']['tipo']);
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            session()->flash('error_modal', 'Ocurrió un error. Por favor, inténtelo nuevamente.');
            return;
        }
    }

    public function edit_data($id){
        $transportistasEdit = Transportista::find(base64_decode($id));
        if ($transportistasEdit){
            $this->transportista_ruc = $transportistasEdit->transportista_ruc;
            $this->transportista_razon_social = $transportistasEdit->transportista_razon_social;
            $this->transportista_nom_comercial = $transportistasEdit->transportista_nom_comercial;
            $this->transportista_direccion = $transportistasEdit->transportista_direccion;
            $this->transportista_telefono = $transportistasEdit->transportista_telefono;
            $this->transportista_gmail = $transportistasEdit->transportista_gmail;
            $this->id_transportista = $transportistasEdit->id_transportista;
        }
    }

    public function btn_disable($id_transpor,$esta){
        $id = base64_decode($id_transpor);
        $status = $esta;
        if ($id){
            $this->id_transportista = $id;
            $this->transportista_estado = $status;
            if ($status == 0){
                $this->message_delete_transportista = "¿Está seguro que desea deshabilitar este registro?";
            }else{
                $this->message_delete_transportista = "¿Está seguro que desea habilitar este registro?";
            }
        }
    }

    public function disable_transportistas(){
        try {
            if (!Gate::allows('disable_transportistas')) {
                session()->flash('error_delete', 'No tiene permisos para cambiar los estados de este registro.');
                return;
            }

            $this->validate([
                'id_transportista' => 'required|integer',
                'transportista_estado' => 'required|integer',
            ], [
                'id_transportista.required' => 'El identificador es obligatorio.',
                'id_transportista.integer' => 'El identificador debe ser un número entero.',

                'transportista_estado.required' => 'El estado es obligatorio.',
                'transportista_estado.integer' => 'El estado debe ser un número entero.',
            ]);

            DB::beginTransaction();
            $transportistasDelete = Transportista::find($this->id_transportista);
            $transportistasDelete->transportista_estado = $this->transportista_estado;
            if ($transportistasDelete->save()) {
                DB::commit();
                $this->dispatch('hide_modal_delete_transportistas');
                if ($this->transportista_estado == 0){
                    session()->flash('success', 'Registro deshabilitado correctamente.');
                }else{
                    session()->flash('success', 'Registro habilitado correctamente.');
                }
            } else {
                DB::rollBack();
                session()->flash('error_delete', 'No se pudo cambiar el estado del registro.');
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

    public function save_transportista(){
        try {
            $this->validate([
                'transportista_ruc' => 'required|size:11',
                'transportista_razon_social' => 'required|string',
                'transportista_nom_comercial' => 'required|string',
                'transportista_direccion' => 'required|string',
                'transportista_telefono' => 'nullable|digits:9',
                'transportista_gmail' => 'nullable|email|max:200',
                'transportista_estado' => 'nullable|integer',
                'id_transportista' => 'nullable|integer',
            ], [
                'transportista_ruc.required' => 'El RUC es obligatorio',
                'transportista_ruc.string' => 'El RUC debe ser una cadena de texto.',
                'transportista_ruc.size' => 'El número RUC debe tener exactamente 11 caracteres.',

                'transportista_razon_social.required' => 'La razon social es obligatorio.',
                'transportista_razon_social.string' => 'La razon social debe ser una cadena de texto.',

                'transportista_nom_comercial.required' => 'La dirección es obligatorio.',
                'transportista_nom_comercial.string' => 'La dirección debe ser una cadena de texto.',

                'transportista_direccion.required' => 'El nombre comercial es obligatorio.',
                'transportista_direccion.string' => 'El nombre comercial debe ser una cadena de texto.',

                'transportista_telefono.digits' => 'El número de teléfono debe tener exactamente 9 caracteres.',

                'transportista_gmail.email' => 'El correo electrónico debe ser un email válido.',

                'transportista_estado.integer' => 'El estado debe ser un número entero.',

                'id_transportista.integer' => 'El identificador debe ser un número entero.',
            ]);

            if (!$this->id_transportista) { // INSERT
                if (!Gate::allows('create_transportistas')) {
                    session()->flash('error_modal', 'No tiene permisos para crear.');
                    return;
                }

                $validar = DB::table('transportistas')->where('transportista_ruc', '=',$this->transportista_ruc)->exists();
                if (!$validar){
                    $microtime = microtime(true);
                    DB::beginTransaction();
                    $transportistas_save = new Transportista();
                    $transportistas_save->id_users = Auth::id();
                    $transportistas_save->transportista_ruc = $this->transportista_ruc;
                    $transportistas_save->transportista_razon_social = $this->transportista_razon_social;
                    $transportistas_save->transportista_nom_comercial = $this->transportista_nom_comercial;
                    $transportistas_save->transportista_direccion = $this->transportista_direccion;
                    $transportistas_save->transportista_telefono = $this->transportista_telefono;
                    $transportistas_save->transportista_gmail = $this->transportista_gmail;
                    $transportistas_save->transportista_microtime = $microtime;
                    $transportistas_save->transportista_estado = 1;

                    if ($transportistas_save->save()) {
                        DB::commit();
                        $this->dispatch('hide_modal_transportistas');
                        session()->flash('success', 'Registro guardado correctamente.');

                    } else {
                        DB::rollBack();
                        session()->flash('error_modal', 'Ocurrió un error al guardar el registro.');
                        return;
                    }
                } else{
                    session()->flash('error_modal', 'El RUC ingresado ya está registrado. Por favor, verifica los datos o ingresa un RUC diferente.');
                    return;
                }
            } else {
                if (!Gate::allows('update_transportistas')) {
                    session()->flash('error_modal', 'No tiene permisos para actualizar este registro.');
                    return;
                }

                $validar_update = DB::table('transportistas')
                    ->where('id_transportista', '<>',$this->id_transportista)
                    ->where('transportista_ruc', '=',$this->transportista_ruc)
                    ->exists();
                if (!$validar_update){
                    DB::beginTransaction();
                    $transportistas_update = Transportista::findOrFail($this->id_transportista);
                    $transportistas_update->transportista_ruc = $this->transportista_ruc;
                    $transportistas_update->transportista_razon_social = $this->transportista_razon_social;
                    $transportistas_update->transportista_nom_comercial = $this->transportista_nom_comercial;
                    $transportistas_update->transportista_direccion = $this->transportista_direccion;
                    $transportistas_update->transportista_telefono = $this->transportista_telefono;
                    $transportistas_update->transportista_gmail = $this->transportista_gmail;

                    if (!$transportistas_update->save()) {
                        session()->flash('error_modal', 'No se pudo actualizar el registro.');
                        return;
                    }
                    DB::commit();
                    $this->dispatch('hide_modal_transportistas');
                    session()->flash('success', 'Registro actualizado correctamente.');
                } else{
                    session()->flash('error_modal', 'El RUC ingresado ya está registrado. Por favor, verifica los datos o ingresa un RUC diferente.');
                    return;
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error_modal', 'Ocurrió un error al guardar el registro. Por favor, inténtelo nuevamente.');
        }
    }

}
