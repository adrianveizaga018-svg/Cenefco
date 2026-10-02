<?php

namespace App\Infrastructure\Ajustes\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $table = 'ajustes';

    public $timestamps = false;

    protected $primaryKey = 'clave';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'descripcion',
        'editable',
    ];

    protected $casts = [
        'editable' => 'boolean',
    ];
}
