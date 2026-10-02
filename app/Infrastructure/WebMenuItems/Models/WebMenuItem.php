<?php
namespace App\Infrastructure\WebMenuItems\Models;
use Illuminate\Database\Eloquent\Model;
class WebMenuItem extends Model {
    protected $table = 'web_menu_items';
    protected $fillable = ['menu_id', 'parent_id', 'titulo', 'url', 'orden', 'activo'];
}
