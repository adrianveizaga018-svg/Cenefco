<?php
namespace App\Http\Requests\GaleriasCategorias;
use Illuminate\Foundation\Http\FormRequest;
class StoreGaleriaCategoriaRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'nombre' => ['required','string','max:200'],
            'descripcion' => ['nullable','string'],
            'orden' => ['nullable','integer'],
            'activo' => ['nullable','boolean'],
        ];
    }
}
