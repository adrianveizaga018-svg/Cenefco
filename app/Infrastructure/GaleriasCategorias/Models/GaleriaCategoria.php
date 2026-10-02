<?php
namespace App\Infrastructure\GaleriasCategorias\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class GaleriaCategoria extends Model {
    protected $table = 'web_galeria_categoria';
    protected $fillable = ['nombre','slug','descripcion','orden','activo'];
    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
    protected static function boot(): void {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre);
            }
        });
        static::updating(function (self $model) {
            if ($model->isDirty('nombre') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->nombre);
            }
        });
    }
}
