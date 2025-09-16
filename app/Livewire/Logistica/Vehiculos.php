<?php

namespace App\Livewire\Logistica;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Models\Logs;
use App\Models\Vehiculo;
use App\Models\Transportista;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Vehiculos extends Component{
    use WithPagination, WithoutUrlPagination;
    private $logs;
    private $vehiculo;
    private $transportista;
    public function __construct(){
        $this->logs = new Logs();
        $this->vehiculo = new Vehiculo();
        $this->transportista = new Transportista();
    }
    public $search_vehiculo;
    public $pagination_vehiculo = 10;
    public $id_vehiculo = "";
    public $id_transportista = "";
    public $vehiculo_placa = "";
    public $vehiculo_capacidad_peso = "";
    public $vehiculo_ancho = "";
    public $vehiculo_largo = "";
    public $vehiculo_alto = "";
    public $vehiculo_capacidad_volumen = "";
    public $vehiculo_estado = "";
    public $message_delete_vehiculo;

    public function render(){
        $listar_vehiculos = $this->vehiculo->listar_vehiculo_activos($this->search_vehiculo, $this->pagination_vehiculo);
        $listar_transportista = $this->transportista->listar_trasnportistas_activos();
        return view('livewire.logistica.vehiculos', compact('listar_vehiculos', 'listar_transportista'));
    }

    public function clear_form(){
        $this->id_vehiculo = "";
        $this->id_transportista = "";
        $this->vehiculo_placa = "";
        $this->vehiculo_capacidad_peso = "";
        $this->vehiculo_ancho = "";
        $this->vehiculo_largo = "";
        $this->vehiculo_alto = "";
        $this->vehiculo_capacidad_volumen = "";
        $this->vehiculo_estado = "";
    }

    public function calcularVolumen(){
        $ancho = floatval($this->vehiculo_ancho);
        $largo = floatval($this->vehiculo_largo);
        $alto = floatval($this->vehiculo_alto);
        $this->vehiculo_capacidad_volumen = $ancho * $largo * $alto;
    }

    public function edit_data($id) {
        $Edit = Vehiculo::find(base64_decode($id));

        if ($Edit) {
            $this->id_transportista = $Edit->id_transportista;
            $this->vehiculo_placa = $Edit->vehiculo_placa;
            $this->vehiculo_capacidad_peso = $Edit->vehiculo_capacidad_peso;
            $this->vehiculo_ancho = $Edit->vehiculo_ancho;
            $this->vehiculo_largo = $Edit->vehiculo_largo;
            $this->vehiculo_alto = $Edit->vehiculo_alto;
            $this->vehiculo_capacidad_volumen = $Edit->vehiculo_capacidad_volumen;
            $this->vehiculo_estado = $Edit->vehiculo_estado;
            $this->id_vehiculo = $Edit->id_vehiculo;
        }
    }

    public function btn_disable($id_vehiculo,$estado){
        $id = base64_decode($id_vehiculo);
        $status = $estado;
        if ($id){
            $this->id_vehiculo = $id;
            $this->vehiculo_estado = $status;
            if ($status == 0){
                $this->message_delete_vehiculo = "¿Está seguro que desea deshabilitar este registro?";
            }else{
                $this->message_delete_vehiculo = "¿Está seguro que desea habilitar este registro?";
            }
        }
    }

    public function disable_vehiculo(){
        try {
            if (!Gate::allows('disable_vehiculo')) {
                session()->flash('error_delete', 'No tiene permisos para cambiar los estados de este registro.');
                return;
            }

            $this->validate([
                'id_vehiculo' => 'required|integer',
                'vehiculo_estado' => 'required|integer',
            ], [
                'id_vehiculo.required' => 'El identificador es obligatorio.',
                'id_vehiculo.integer' => 'El identificador debe ser un número entero.',

                'vehiculo_estado.required' => 'El estado es obligatorio.',
                'vehiculo_estado.integer' => 'El estado debe ser un número entero.',
            ]);

            DB::beginTransaction();
            $vehiculoDelete = Vehiculo::find($this->id_vehiculo);
            $vehiculoDelete->vehiculo_estado = $this->vehiculo_estado;
            if ($vehiculoDelete->save()) {
                DB::commit();
                $this->dispatch('hide_modal_delete_vehiculo');
                if ($this->vehiculo_estado == 0){
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
            session()->flash('error', 'Ocurrió un error. Por favor, inténtelo nuevamente.');
        }
    }

    public function save_vehiculo() {
        try {
            $this->validate([
                'id_transportista' => 'required|integer',
                'vehiculo_placa' => 'required|string',
                'vehiculo_capacidad_peso' => 'required|numeric',
                'vehiculo_ancho' => 'required|numeric',
                'vehiculo_largo' => 'required|numeric',
                'vehiculo_alto' => 'required|numeric',
                'vehiculo_capacidad_volumen' => 'required|numeric',
                'vehiculo_estado' => 'nullable|integer',
                'id_vehiculo' => 'nullable|integer',
            ], [
                'id_transportista.required' => 'El transportista es obligatoria.',
                'id_transportista.integer' => 'El transportista debe tener un valor numérico.',

                'vehiculo_placa.required' => 'La placa es obligatoria.',
                'vehiculo_placa.string' => 'La placa debe ser una cadena de texto.',

                'vehiculo_capacidad_peso.required' => 'La capacidad de peso del vehículo es obligatoria.',
                'vehiculo_capacidad_peso.numeric' => 'La capacidad de peso del vehículo debe ser un valor numérico.',

                'vehiculo_ancho.required' => 'El ancho del vehículo es obligatorio.',
                'vehiculo_ancho.numeric' => 'El ancho del vehículo debe ser un valor numérico.',

                'vehiculo_largo.required' => 'El largo del vehículo es obligatorio.',
                'vehiculo_largo.numeric' => 'El largo del vehículo debe ser un valor numérico.',

                'vehiculo_alto.required' => 'La altura del vehículo es obligatoria.',
                'vehiculo_alto.numeric' => 'La altura del vehículo debe ser un valor numérico.',

                'vehiculo_capacidad_volumen.required' => 'La capacidad de volumen del vehículo es obligatoria.',
                'vehiculo_capacidad_volumen.numeric' => 'La capacidad de volumen del vehículo debe ser un valor numérico.',

                'vehiculo_estado.integer' => 'El estado debe ser un número entero.',

                'id_vehiculo.integer' => 'El identificador debe ser un número entero.',
            ]);

            if (!$this->id_vehiculo) { // INSERT
                if (!Gate::allows('create_vehiculo')) {
                    session()->flash('error_modal_agregar', 'No tiene permisos para crear.');
                    return;
                }

                $validar = DB::table('vehiculos')->where('vehiculo_placa', '=', $this->vehiculo_placa)->exists();
                if (!$validar) {
                    $microtime = microtime(true);
                    DB::beginTransaction();
                    $vehiculo_save = new Vehiculo();
                    $vehiculo_save->id_users = Auth::id();
                    $vehiculo_save->id_transportista = $this->id_transportista;
                    $vehiculo_save->vehiculo_placa = $this->vehiculo_placa;
                    $vehiculo_save->vehiculo_capacidad_peso = $this->vehiculo_capacidad_peso;
                    $vehiculo_save->vehiculo_ancho = $this->vehiculo_ancho;
                    $vehiculo_save->vehiculo_largo = $this->vehiculo_largo;
                    $vehiculo_save->vehiculo_alto = $this->vehiculo_alto;
                    $vehiculo_save->vehiculo_capacidad_volumen = $this->vehiculo_capacidad_volumen;
                    $vehiculo_save->vehiculo_microtime = $microtime;
                    $vehiculo_save->vehiculo_estado = 1;

                    if ($vehiculo_save->save()) {
                        DB::commit();
                        $this->dispatch('hide_modal_vehiculo');
                        session()->flash('success', 'Registro guardado correctamente.');
                    } else {
                        DB::rollBack();
                        session()->flash('error_modal_agregar', 'Ocurrió un error al guardar el vehículo.');
                        return;
                    }
                } else {
                    session()->flash('error_modal_agregar', 'La placa ingresada para el vehículo ya está registrada.');
                    return;
                }
            } else { // UPDATE
                if (!Gate::allows('update_vehiculos')) {
                    session()->flash('error_modal_agregar', 'No tiene permisos para actualizar este registro.');
                    return;
                }

                // Validar si la placa ya está registrada en otro vehículo
                $validar_update = DB::table('vehiculos')
                    ->where('id_vehiculo', '<>', $this->id_vehiculo)
                    ->where('vehiculo_placa', '=', $this->vehiculo_placa)
                    ->exists();

                if (!$validar_update) {
                    DB::beginTransaction();
                    // Actualizar los datos del vehículo
                    $vehiculo_update = Vehiculo::findOrFail($this->id_vehiculo);
                    $vehiculo_update->id_transportista = $this->id_transportista;
                    $vehiculo_update->vehiculo_placa = $this->vehiculo_placa;
                    $vehiculo_update->vehiculo_capacidad_peso = $this->vehiculo_capacidad_peso;
                    $vehiculo_update->vehiculo_ancho = $this->vehiculo_ancho;
                    $vehiculo_update->vehiculo_largo = $this->vehiculo_largo;
                    $vehiculo_update->vehiculo_alto = $this->vehiculo_alto;
                    $vehiculo_update->vehiculo_capacidad_volumen = $this->vehiculo_capacidad_volumen;
                    $vehiculo_update->vehiculo_estado = 1;

                    // Guardar cambios en el vehículo
                    if (!$vehiculo_update->save()) {
                        DB::rollBack();
                        session()->flash('error_modal_agregar', 'No se pudo actualizar el registro del vehículo.');
                        return;
                    }
                    DB::commit();
                    $this->dispatch('hide_modal_vehiculo');
                    session()->flash('success', 'Registro actualizado correctamente.');
                } else {
                    session()->flash('error_modal_agregar', 'La placa ingresada para el vehículo ya está registrada.');
                    return;
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logs->insertarLog($e);
            session()->flash('error_modal_agregar', 'Ocurrió un error al guardar el registro: ' . $e->getMessage());
        }
    }

}
