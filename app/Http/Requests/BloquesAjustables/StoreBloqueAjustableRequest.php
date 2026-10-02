<?php
namespace App\Http\Requests\BloquesAjustables;
use Illuminate\Foundation\Http\FormRequest;
class StoreBloqueAjustableRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['id_bloqueajustable'=>['required','integer'],'id_pagina'=>['nullable','integer'],'id_bloqueplantilla'=>['nullable','integer'],'titulo'=>['nullable','string']];
    }
}
