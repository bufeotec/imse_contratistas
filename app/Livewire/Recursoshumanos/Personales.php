<?php

namespace App\Livewire\Recursoshumanos;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Logs;
use App\Models\Personal;

class Personales extends Component
{
    use WithPagination, WithoutUrlPagination;

    private $logs;

    public function __construct()
    {
        $this->logs = new Logs();
    }

    // Listado
    public $search_personal = '';
    public $pagination_personal = 10;

    // Formulario
    public $id_personal = '';
    public $personal_nombre = '';
    public $personal_apellido = '';
    public $personal_gmail = '';
    public $personal_telefono = '';
    public $personal_direccion = '';

    public function render()
    {
        $s = trim($this->search_personal);

        $listar_personales = DB::table('personales')
            ->select('id_personal','personal_nombre','personal_apellido','personal_gmail','personal_telefono','personal_estado')
            ->when($s !== '', function ($q) use ($s) {
                $q->where(function ($w) use ($s) {
                    $w->where('personal_nombre', 'like', "%{$s}%")
                        ->orWhere('personal_apellido', 'like', "%{$s}%")
                        ->orWhere('personal_gmail', 'like', "%{$s}%")
                        ->orWhere('personal_telefono', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('id_personal')
            ->paginate($this->pagination_personal);

        return view('livewire.recursoshumanos.personales', compact('listar_personales'));
    }

    public function clear_form()
    {
        $this->reset([
            'id_personal',
            'personal_nombre',
            'personal_apellido',
            'personal_gmail',
            'personal_telefono',
            'personal_direccion',
        ]);
    }

    public function editar($id)
    {
        try {
            $id_decoded = base64_decode($id);
            $p = DB::table('personales')->where('id_personal', $id_decoded)->first();

            if (!$p) {
                session()->flash('error', 'Personal no encontrado.');
                return;
            }

            $this->id_personal = $p->id_personal;
            $this->personal_nombre = $p->personal_nombre;
            $this->personal_apellido  = $p->personal_apellido;
            $this->personal_gmail = $p->personal_gmail;
            $this->personal_telefono = $p->personal_telefono;
            $this->personal_direccion = $p->personal_direccion;

        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            session()->flash('error', 'Ocurrió un error al cargar el registro.');
        }
    }

    public function save_personal(){
        try {
            $this->validate([
                'personal_nombre' => 'required|string',
                'personal_apellido' => 'required|string',
                'personal_gmail' => 'required|email',
                'personal_telefono' => 'required|string|max:9',
                'personal_direccion' => 'required|string',
                'id_personal' => 'nullable|integer',
            ], [
                'personal_nombre.required' => 'El nombre es obligatorio.',
                'personal_apellido.required' => 'El apellido es obligatorio.',
                'personal_gmail.required' => 'El email es obligatorio.',
                'personal_gmail.email' => 'Debe ingresar un email válido.',
                'personal_telefono.required' => 'El teléfono es obligatorio.',
                'personal_telefono.max' => 'El teléfono no debe exceder 9 caracteres.',
                'personal_direccion.required' => 'La dirección es obligatoria.',
            ]);

            if (!$this->id_personal) { // INSERT
                $microtime = microtime(true);

                DB::beginTransaction();
                $save_personal = new Personal();
                $save_personal->id_users = Auth::id();
                $save_personal->personal_nombre = $this->personal_nombre;
                $save_personal->personal_apellido = $this->personal_apellido;
                $save_personal->personal_gmail = $this->personal_gmail;
                $save_personal->personal_telefono = $this->personal_telefono;
                $save_personal->personal_direccion = $this->personal_direccion;
                $save_personal->personal_microtime = $microtime;
                $save_personal->personal_estado = 1;

                if ($save_personal->save()) {
                    DB::commit();
                    $this->dispatch('hideModal');
                    session()->flash('success', 'Registro guardado correctamente.');
                } else {
                    DB::rollBack();
                    session()->flash('error', 'Ocurrió un error al guardar el personal.');
                    return;
                }
            } else { // UPDATE
                try {
                    DB::beginTransaction();

                    $upd = Personal::find($this->id_personal);
                    if (!$upd) {
                        DB::rollBack();
                        session()->flash('error', 'Personal no encontrado.');
                        return;
                    }

                    $upd->personal_nombre = $this->personal_nombre;
                    $upd->personal_apellido = $this->personal_apellido;
                    $upd->personal_gmail = $this->personal_gmail;
                    $upd->personal_telefono = $this->personal_telefono;
                    $upd->personal_direccion = $this->personal_direccion;

                    if ($upd->save()) {
                        DB::commit();
                        $this->dispatch('hideModal');
                        session()->flash('success', 'Registro actualizado correctamente.');
                    } else {
                        DB::rollBack();
                        session()->flash('error', 'Ocurrió un error al actualizar el personal.');
                        return;
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->logs->insertarLog($e);
                    session()->flash('error', 'Ocurrió un error al guardar el registro. Por favor, inténtelo nuevamente.');
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
}
