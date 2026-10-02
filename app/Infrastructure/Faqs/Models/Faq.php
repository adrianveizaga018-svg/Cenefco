<?php

namespace App\Infrastructure\Faqs\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'web_faq';

    protected $fillable = [
        'pregunta',
        'respuesta',
        'categoria',
        'programa_id',
        'orden',
        'activo',
    ];

    protected $casts = [
        'programa_id' => 'integer',
        'orden'       => 'integer',
        'activo'      => 'boolean',
    ];
}
