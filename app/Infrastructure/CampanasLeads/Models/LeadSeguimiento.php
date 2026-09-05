<?php
namespace App\Infrastructure\CampanasLeads\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSeguimiento extends Model
{
    protected $table = 'lead_seguimientos';
    protected $fillable = ['lead_id','vendedor_id','tipo','resultado','nota','proxima_accion','fecha_proxima_accion'];
    protected $casts = ['fecha_proxima_accion' => 'date'];
    
    public function lead() {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
