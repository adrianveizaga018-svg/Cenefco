<?php

namespace App\Infrastructure\Fotos\Models;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    protected $table      = 't_foto';
    protected $primaryKey = 'id_foto';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_foto', 'id_us_reg', 'num_foto',
        'titulo_foto', 'descripcion_foto', 'foto',
        'fecha_foto', 'estado', 'fecha_reg',
    ];
}
