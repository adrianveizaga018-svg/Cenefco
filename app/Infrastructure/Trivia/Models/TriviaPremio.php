<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriviaPremio extends Model
{
    use SoftDeletes;

    protected $table = 'trivia_premios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'imagen_url',
        'costo_puntos',
        'stock',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'costo_puntos' => 'integer',
        'stock' => 'integer',
        'orden' => 'integer',
    ];

    public function canjes()
    {
        return $this->hasMany(TriviaCanje::class, 'premio_id');
    }
}
