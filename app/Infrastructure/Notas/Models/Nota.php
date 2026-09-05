<?php

namespace App\Infrastructure\Notas\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 't_nota';
    protected $primaryKey = 'id_not';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_not',
        'id_us_reg',
        'periodo',
        'gestion',
        'id_imp',
        'id_us',
        'id_mat',
        'nota',
        'nota_seg',
        'paralelo',
        'mostrarcert_notas',
        'estado',
        'fecha_reg',
    ];
}
