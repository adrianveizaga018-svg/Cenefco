<?php
namespace App\Http\Requests\GaleriasVideos;
use Illuminate\Foundation\Http\FormRequest;
class StoreGaleriaVideoRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'titulo' => ['required','string','max:300'],
            'url_video' => ['required','string','max:500'],
            'descripcion' => ['nullable','string'],
            'plataforma' => ['nullable','string','max:50'],
            'video_id' => ['nullable','string','max:100'],
            'miniatura_url' => ['nullable','string','max:255'],
            'duracion' => ['nullable','string','max:20'],
            'tipo' => ['nullable','string','max:100'],
            'programa_id' => ['nullable','integer'],
            'destacado' => ['nullable','boolean'],
            'orden' => ['nullable','integer'],
            'vistas' => ['nullable','integer'],
            'activo' => ['nullable','boolean'],
        ];
    }
}
