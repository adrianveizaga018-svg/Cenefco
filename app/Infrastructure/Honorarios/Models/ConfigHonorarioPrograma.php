<?php

namespace App\Infrastructure\Honorarios\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigHonorarioPrograma extends Model
{
    protected $table = 'web_config_honorario_programa';

    protected $fillable = [
        'id_programa',
        'tipo_honorario',
        'monto_fijo',
        'monto_por_dia',
    ];

    protected $casts = [
        'monto_fijo'    => 'decimal:2',
        'monto_por_dia' => 'decimal:2',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
