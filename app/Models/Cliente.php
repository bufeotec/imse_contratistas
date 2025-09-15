<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Cliente extends Model{
    use HasFactory;
    protected $table = "clientes";
    protected $primaryKey = "id_cliente";

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }

    public function tipoCliente()
    {
        return $this->belongsTo(Tipocliente::class, 'id_tipo_cliente', 'id_tipo_cliente');
    }

    public function listar_clientes($tipo_doc, $tipo_cliente, $search, $pagination, $order = 'desc') {
        try {
            $query = DB::table('clientes as c')
                ->join('tipos_documentos as td', 'c.id_tipo_documento', 'td.id_tipo_documento')
                ->where('c.cliente_estado', 1);

            // Filtro por tipo de documento (solo si tiene valor)
            if (!empty($tipo_doc)) {
                $query->where('td.id_tipo_documento', $tipo_doc);
            }

            // Filtro de búsqueda general
            $query->where(function($q) use ($search) {
                $q->where('c.cliente_numero_documento', 'like', '%' . $search . '%')
                    ->orWhere('c.cliente_razon_social', 'like', '%' . $search . '%');
            });

            $query->orderBy('c.id_cliente', $order);

            $result = $query->paginate($pagination);

            // Numeración de filas
            $result->getCollection()->transform(function($item, $key) use ($result) {
                $item->numero = $key + 1 + ($result->currentPage() - 1) * $result->perPage();
                return $item;
            });
        } catch (\Exception $e){
            $this->logs->insertarLog($e);
            return new LengthAwarePaginator(collect([]), 0, $pagination);
        }
        return $result;
    }

    public function listar_todos_cliente(){
        try {
            $result = DB::table('clientes')->where('cliente_estado','=',1)->get();
        }catch (\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function listar_cliente_x_documento($num_documento){
        try {
            $result = DB::table('clientes')
                ->where('cliente_numero_documento', '=',$num_documento)
                ->first();;
        }catch (\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function listar_info_cliente_x_id($id) {
        return DB::table('clientes as c')
            ->join('tipos_documentos as td', 'c.id_tipo_documento', 'td.id_tipo_documento')
            ->where('c.cliente_estado', 1)
            ->where('c.id_cliente', $id)
            ->first();
    }

    public function listar_clientes_busqueda($escrito)
    {
        try {
            $result = DB::table('clientes')
                ->where('cliente_nombre', 'like', '%' . $escrito . '%')
                ->orWhere('cliente_dni','like', '%' . $escrito . '%')
                ->orWhere('cliente_direccion','like', '%' . $escrito . '%')
                ->orWhere('cliente_telefono','like', '%' . $escrito . '%')
                ->orWhere('cliente_email','like', '%' . $escrito . '%')
                ->limit(1)
                ->get();
            ;
        }catch (\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function obtener_cliente_por_id($id_cl) {
        try {
            return DB::table('clientes as c')
                ->leftJoin('tipos_clientes as tc', 'c.id_tipo_cliente', '=', 'tc.id_tipo_cliente')
                ->leftJoin('tipos_documentos as td', 'c.id_tipo_documento', '=', 'td.id_tipo_documento')
                ->where('c.id_cliente', $id_cl)
                ->where('c.cliente_estado', 1)
                ->select([
                    'c.cliente_numero_documento',
                    'c.cliente_razon_social',
                    'c.cliente_nombre_comercial',
                    'c.cliente_direccion',
                    'c.cliente_telefono',
                    'c.cliente_email',
                    'c.cliente_persona_contacto',
                    'c.cliente_numero_contacto',
                    'c.cliente_observacion',
                    'td.tipo_documento_identidad_abr',
                    'tc.tipo_cliente_concepto',
                ])
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}
