<?php
namespace App\Infrastructure\GaleriasVideos\Models;
use Illuminate\Database\Eloquent\Model;
class GaleriaVideo extends Model {
    protected $table = 'web_galeria_video';
    protected $fillable = [
        'titulo','descripcion','plataforma','url_video','video_id',
        'miniatura_url','duracion','tipo','programa_id','destacado','orden','vistas','activo',
    ];
    protected $casts = [
        'programa_id' => 'integer',
        'destacado' => 'boolean',
        'orden' => 'integer',
        'vistas' => 'integer',
        'activo' => 'boolean',
    ];
}
