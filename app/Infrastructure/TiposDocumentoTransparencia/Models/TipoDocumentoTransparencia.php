<?php

namespace App\Infrastructure\TiposDocumentoTransparencia\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoTransparencia extends Model
{
    protected $table = 'tipos_documento_transparencia';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
