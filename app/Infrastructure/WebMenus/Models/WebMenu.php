<?php
namespace App\Infrastructure\WebMenus\Models;
use Illuminate\Database\Eloquent\Model;
class WebMenu extends Model {
    protected $table = 'web_menus';
    protected $fillable = ['nombre', 'descripcion', 'activo'];
}
