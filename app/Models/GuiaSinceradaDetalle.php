<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaSinceradaDetalle extends Model
{
    use HasFactory;

    protected $table = "guia_sincerada_detalle";
    protected $primaryKey = "id_guia_sincerada_detalle";

    public $timestamps = true;

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }
}
