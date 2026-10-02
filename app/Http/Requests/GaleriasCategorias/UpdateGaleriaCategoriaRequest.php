<?php
namespace App\Http\Requests\GaleriasCategorias;
use Illuminate\Foundation\Http\FormRequest;
class UpdateGaleriaCategoriaRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'nombre' => ['sometimes','nullable','string','max:200'],
            'descripcion' => ['sometimes','nullable','string'],
            'orden' => ['sometimes','nullable','integer'],
            'activo' => ['sometimes','nullable','boolean'],
        ];
    }
}
