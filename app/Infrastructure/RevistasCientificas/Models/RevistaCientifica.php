<?php

namespace App\Infrastructure\RevistasCientificas\Models;

use Illuminate\Database\Eloquent\Model;

class RevistaCientifica extends Model
{
    protected $table      = 't_revistacientifica';
    protected $primaryKey = 'id_revistacientifica';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_revistacientifica', 'id_us_reg', 'num_revistacientifica',
        'titulo_revistacientifica', 'descripcion_revistacientifica',
        'fecha_publicacion', 'archivo',
        'estado', 'slug', 'fecha_reg',
    ];
}
