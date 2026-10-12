<?php

namespace App\Infrastructure\CampanasLeads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        'campana_lead_id',
        'nombre',
        'celular',
        'correo',
        'profesion',
        'estado',
        'vendedor_asignado_id',
        'programa_id',
        'ci',
        'origen',
        'motivo_perdida',
        'campana_publicidad_id',
        'id_us',
        'id_ins',
        'asignado_at',
        'ultimo_contacto_at',
        'proxima_accion_at',
        'cerrado_at',
        'alerta_enviada_at',
    ];

    protected $casts = [
        'asignado_at'        => 'datetime',
        'ultimo_contacto_at' => 'datetime',
        'proxima_accion_at'  => 'datetime',
        'cerrado_at'         => 'datetime',
        'alerta_enviada_at'  => 'datetime',
    ];

    public function campanaLead()
    {
        return $this->belongsTo(CampanaLead::class, 'campana_lead_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(LeadSeguimiento::class, 'lead_id');
    }
}
