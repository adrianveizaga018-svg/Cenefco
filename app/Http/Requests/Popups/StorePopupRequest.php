<?php
namespace App\Http\Requests\Popups;
use Illuminate\Foundation\Http\FormRequest;
class StorePopupRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'titulo' => ['nullable','string','max:300'], 'contenido' => ['nullable','string'],
            'imagen_url' => ['nullable','string','max:255'], 'enlace_url' => ['nullable','string','max:255'],
            'enlace_texto' => ['nullable','string','max:100'], 'posicion' => ['nullable','string','max:50'],
            'delay_segundos' => ['nullable','integer'], 'mostrar_una_vez_sesion' => ['nullable','boolean'],
            'mostrar_una_vez_siempre' => ['nullable','boolean'], 'paginas_mostrar' => ['nullable','string','max:500'],
            'activo' => ['nullable','boolean'], 'fecha_inicio' => ['nullable','date'], 'fecha_fin' => ['nullable','date'],
        ];
    }
}
