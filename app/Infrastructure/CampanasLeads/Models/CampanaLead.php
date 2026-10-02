<?php

namespace App\Infrastructure\CampanasLeads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampanaLead extends Model
{
    use SoftDeletes;

    protected $table = 'campanas_leads';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'campana_lead_id')->orderByDesc('created_at');
    }
}
