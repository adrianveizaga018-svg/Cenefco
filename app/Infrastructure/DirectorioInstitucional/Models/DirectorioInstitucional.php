<?php

namespace App\Infrastructure\DirectorioInstitucional\Models;

use App\Infrastructure\Secretarias\Models\Secretaria;
use Illuminate\Database\Eloquent\Model;

class DirectorioInstitucional extends Model
{
    protected $table = 'directorio_institucional';

    public $timestamps = false;

    protected $fillable = [
        'secretaria_id',
        'nombre_unidad',
        'descripcion',
        'titular',
        'cargo_responsable',
        'direccion_fisica',
        'telefono_principal',
        'telefono_secundario',
        'email_institucional',
        'foto_url',
        'horario_lunes_viernes',
        'horario_sabado',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id');
    }
}
