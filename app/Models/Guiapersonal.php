<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guiapersonal extends Model{
    use HasFactory;
    protected $table = "guias_personas";
    protected $primaryKey = "id_guia_personal";

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }
}
