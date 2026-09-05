<?php
namespace App\Http\Requests\WhatsappGrupos;
use Illuminate\Foundation\Http\FormRequest;
class StoreWhatsappGrupoRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'imparte_id' => ['required','integer'],
            'nombre' => ['required','string','max:200'],
            'enlace_invitacion' => ['required','string','max:500'],
            'capacidad_maxima' => ['nullable','integer'],
            'miembros_actuales' => ['nullable','integer'],
            'descripcion' => ['nullable','string','max:300'],
            'activo' => ['nullable','boolean'],
            'orden' => ['nullable','integer'],
            'fecha_expiracion_enlace' => ['nullable','date'],
        ];
    }
}
