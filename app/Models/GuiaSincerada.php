<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaSincerada extends Model
{
    use HasFactory;

    protected $table = "guia_sincerada";
    protected $primaryKey = "id_guia_sincerada";

    public $timestamps = true;

    private $logs;
    public function __construct(){
        parent::__construct();
        $this->logs = new Logs();
    }
}
