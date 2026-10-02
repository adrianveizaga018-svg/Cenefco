<?php
namespace App\Http\Requests\WhatsappGrupos;
use Illuminate\Foundation\Http\FormRequest;
class UpdateWhatsappGrupoRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'imparte_id' => ['sometimes','nullable','integer'],
            'nombre' => ['sometimes','nullable','string','max:200'],
            'enlace_invitacion' => ['sometimes','nullable','string','max:500'],
            'capacidad_maxima' => ['sometimes','nullable','integer'],
            'miembros_actuales' => ['sometimes','nullable','integer'],
            'descripcion' => ['sometimes','nullable','string','max:300'],
            'activo' => ['sometimes','nullable','boolean'],
            'orden' => ['sometimes','nullable','integer'],
            'fecha_expiracion_enlace' => ['sometimes','nullable','date'],
        ];
    }
}
