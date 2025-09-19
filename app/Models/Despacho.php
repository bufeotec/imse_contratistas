<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    use HasFactory;
    protected $table = "despachos";
    protected $primaryKey = "id_despacho";
    public $timestamps = true;

    public function despacho_detalle()
    {
        return $this->hasMany(DespachoDetalle::class, 'id_despacho');
    }
}
