<?php

namespace App\Infrastructure\Gastos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaGasto extends Model
{
    use SoftDeletes;

    protected $table = 'categoria_gasto';

    protected $fillable = [
        'nombre',
        'linea_negocio',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'categoria_gasto_id');
    }
}
