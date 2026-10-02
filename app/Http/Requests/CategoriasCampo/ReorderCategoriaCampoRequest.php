<?php

namespace App\Http\Requests\CategoriasCampo;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCategoriaCampoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'         => ['required', 'array'],
            'items.*.id'    => ['required', 'integer'],
            'items.*.orden' => ['required', 'integer'],
        ];
    }
}
