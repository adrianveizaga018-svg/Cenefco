<?php

namespace App\Infrastructure\CertConfigProgramas\Models;

use Illuminate\Database\Eloquent\Model;

class CertConfigPrograma extends Model
{
    protected $table = 'web_cert_config_programa';

    protected $fillable = [
        'programa_id',
        'activo',
        'titulo',
        'descripcion',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
