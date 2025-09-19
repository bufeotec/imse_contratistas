<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DespachoDetalle extends Model
{
    use HasFactory;
    protected $table = "despachos_detalle";
    protected $primaryKey = "id_despacho_detalle";
    public $timestamps = true;
}
