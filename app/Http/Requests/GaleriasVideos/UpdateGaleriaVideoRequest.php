<?php
namespace App\Http\Requests\GaleriasVideos;
use Illuminate\Foundation\Http\FormRequest;
class UpdateGaleriaVideoRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'titulo' => ['sometimes','nullable','string','max:300'],
            'url_video' => ['sometimes','nullable','string','max:500'],
            'descripcion' => ['sometimes','nullable','string'],
            'plataforma' => ['sometimes','nullable','string','max:50'],
            'video_id' => ['sometimes','nullable','string','max:100'],
            'miniatura_url' => ['sometimes','nullable','string','max:255'],
            'duracion' => ['sometimes','nullable','string','max:20'],
            'tipo' => ['sometimes','nullable','string','max:100'],
            'programa_id' => ['sometimes','nullable','integer'],
            'destacado' => ['sometimes','nullable','boolean'],
            'orden' => ['sometimes','nullable','integer'],
            'vistas' => ['sometimes','nullable','integer'],
            'activo' => ['sometimes','nullable','boolean'],
        ];
    }
}
