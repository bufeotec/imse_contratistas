<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = "personales";
    protected $primaryKey = "id_personal";

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }
}
