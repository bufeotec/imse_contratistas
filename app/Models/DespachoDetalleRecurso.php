<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DespachoDetalleRecurso extends Model
{
    use HasFactory;
    protected $table = "despachos_detalle_recurso";
    protected $primaryKey = "id_despacho_detalle_recurso";
    public $timestamps = true;
}
