<?php
namespace App\Infrastructure\Popups\Models;
use Illuminate\Database\Eloquent\Model;
class Popup extends Model {
    protected $table = 'web_popup';
    protected $fillable = ['titulo','contenido','imagen_url','enlace_url','enlace_texto','posicion','delay_segundos','mostrar_una_vez_sesion','mostrar_una_vez_siempre','paginas_mostrar','activo','fecha_inicio','fecha_fin'];
    protected $casts = ['delay_segundos'=>'integer','mostrar_una_vez_sesion'=>'boolean','mostrar_una_vez_siempre'=>'boolean','activo'=>'boolean','fecha_inicio'=>'datetime','fecha_fin'=>'datetime'];
}
