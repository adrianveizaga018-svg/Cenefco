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
