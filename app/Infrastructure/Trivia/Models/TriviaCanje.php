<?php

namespace App\Infrastructure\Trivia\Models;

use App\Infrastructure\Usuarios\Models\User;
use Illuminate\Database\Eloquent\Model;

class TriviaCanje extends Model
{
    protected $table = 'trivia_canjes';

    protected $fillable = [
        'usuario_id',
        'premio_id',
        'codigo',
        'costo_puntos',
        'estado',
        'nota',
        'entregado_por',
        'fecha_resolucion',
    ];

    protected $casts = [
        'costo_puntos' => 'integer',
        'fecha_resolucion' => 'datetime',
    ];

    public function premio()
    {
        return $this->belongsTo(TriviaPremio::class, 'premio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function entregadoPorUsuario()
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }
}
