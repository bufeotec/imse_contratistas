<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guiarecurso extends Model{
    use HasFactory;
    protected $table = "guias_recursos";
    protected $primaryKey = "id_guia_recurso";

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }
}
