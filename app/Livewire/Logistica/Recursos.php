<?php

namespace App\Livewire\Logistica;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Models\Logs;
use App\Models\Tiporecurso;
use App\Models\Medida;
use App\Models\Recurso;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Recursos extends Component{
    use WithPagination, WithoutUrlPagination;
    private $logs;
    private $tiporecurso;
    private $medida;
    private $recurso;
    public function __construct(){
        $this->logs = new Logs();
        $this->tiporecurso = new Tiporecurso();
        $this->medida = new Medida();
        $this->recurso = new Recurso();
    }
    public $search_recurso;
    public $pagination_recurso = 10;
    public $id_recurso  = "";
    public $id_tipo_recurso  = "";
    public $id_medida  = "";
    public $recurso_cantidad  = "";
    public $recurso_estado  = "";
    public $message_delete_recurso;

    public function render(){
        $listar_recursos = $this->recurso->listar_recursos_activos($this->search_recurso, $this->pagination_recurso);
        $listar_tipos_recursos = $this->tiporecurso->listar_tipos_recursos();
        $listar_medidas = $this->medida->listar_medidas();
        return view('livewire.logistica.recursos', compact('listar_recursos', 'listar_tipos_recursos', 'listar_medidas'));
    }

    public function clear_form(){
        $this->id_recurso = "";
        $this->id_tipo_recurso = "";
        $this->id_medida = "";
        $this->recurso_cantidad = "";
        $this->recurso_estado = "";
    }

    public function edit_data($id) {
        $Edit = Recurso::find(base64_decode($id));
        if ($Edit) {
            $this->id_tipo_recurso = $Edit->id_tipo_recurso;
            $this->id_medida = $Edit->id_medida;
            $this->recurso_cantidad = $Edit->recurso_cantidad;
            $this->recurso_estado = $Edit->recurso_estado;
            $this->id_recurso = $Edit->id_recurso;
        }
    }

    public function btn_disable($id_vehiculo,$estado){
        $id = base64_decode($id_vehiculo);
        $status = $estado;
        if ($id){
            $this->id_recurso = $id;
            $this->recurso_estado = $status;
            if ($status == 0){
                $this->message_delete_recurso = "¿Está seguro que desea deshabilitar este registro?";
            }else{
                $this->message_delete_recurso = "¿Está seguro que desea habilitar este registro?";
            }
        }
    }

    public function disable_recurso(){
        try {
            if (!Gate::allows('disable_recurso')) {
                session()->flash('error_delete', 'No tiene permisos para cambiar los estados de este registro.');
                return;
            }

            $this->validate([
                'id_recurso' => 'required|integer',
                'recurso_estado' => 'required|integer',
            ], [
                'id_recurso.required' => 'El identificador es obligatorio.',
                'id_recurso.integer' => 'El identificador debe ser un número entero.',

                'recurso_estado.required' => 'El estado es obligatorio.',
                'recurso_estado.integer' => 'El estado debe ser un número entero.',
            ]);

            DB::beginTransaction();
            $Delete = Recurso::find($this->id_recurso);
            $Delete->recurso_estado = $this->recurso_estado;
            if ($Delete->save()) {
                DB::commit();
                $this->dispatch('hide_modal_delete_recurso');
                if ($this->recurso_estado == 0){
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

    public function save_recurso() {
        try {
            $this->validate([
                'id_tipo_recurso' => 'required|integer',
                'id_medida' => 'required|integer',
                'recurso_cantidad' => 'required|numeric',
                'recurso_estado' => 'nullable|integer',
                'id_recurso' => 'nullable|integer',
            ], [
                'id_tipo_recurso.required' => 'El tipo de recurso es obligatoria.',
                'id_tipo_recurso.integer' => 'El tipo de recurso debe tener un valor numérico.',

                'id_medida.required' => 'La medida es obligatoria.',
                'id_medida.string' => 'La medida debe ser un valor numérico.',

                'recurso_cantidad.required' => 'La capacidad es obligatoria.',
                'recurso_cantidad.numeric' => 'La capacidad debe ser un valor numérico.',

                'recurso_estado.integer' => 'El estado debe ser un número entero.',

                'id_recurso.integer' => 'El identificador debe ser un número entero.',
            ]);

            if (!$this->id_recurso) { // INSERT
                if (!Gate::allows('create_recurso')) {
                    session()->flash('error_modal_agregar', 'No tiene permisos para crear.');
                    return;
                }
                $microtime = microtime(true);
                DB::beginTransaction();
                $recurso_save = new Recurso();
                $recurso_save->id_users = Auth::id();
                $recurso_save->id_tipo_recurso = $this->id_tipo_recurso;
                $recurso_save->id_medida = $this->id_medida;
                $recurso_save->recurso_cantidad = $this->recurso_cantidad;
                $recurso_save->recurso_estado_movimiento = 1;
                $recurso_save->recurso_microtime = $microtime;
                $recurso_save->recurso_estado = 1;

                if ($recurso_save->save()) {
                    DB::commit();
                    $this->dispatch('hide_modal_recurso');
                    session()->flash('success', 'Registro guardado correctamente.');
                } else {
                    DB::rollBack();
                    session()->flash('error_modal_agregar', 'Ocurrió un error al guardar el recurso.');
                    return;
                }

            } else { // UPDATE
                if (!Gate::allows('update_recurso')) {
                    session()->flash('error_modal_agregar', 'No tiene permisos para actualizar este registro.');
                    return;
                }

                DB::beginTransaction();
                // Actualizar los datos del vehículo
                $recurso_update = Recurso::findOrFail($this->id_recurso);
                $recurso_update->id_tipo_recurso = $this->id_tipo_recurso;
                $recurso_update->id_medida = $this->id_medida;
                $recurso_update->recurso_cantidad = $this->recurso_cantidad;

                // Guardar cambios en el vehículo
                if (!$recurso_update->save()) {
                    DB::rollBack();
                    session()->flash('error_modal_agregar', 'No se pudo actualizar el registro del recurso.');
                    return;
                }
                DB::commit();
                $this->dispatch('hide_modal_recurso');
                session()->flash('success', 'Registro actualizado correctamente.');
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
