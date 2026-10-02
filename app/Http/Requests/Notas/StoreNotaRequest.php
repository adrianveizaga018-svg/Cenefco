<?php

namespace App\Http\Requests\Notas;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_not'    => ['required', 'integer', 'unique:t_nota,id_not'],
            'id_us_reg' => ['nullable', 'integer'],
            'periodo'   => ['nullable', 'string', 'max:200'],
            'gestion'   => ['nullable', 'string', 'max:10'],
            'id_imp'    => ['required', 'integer'],
            'id_us'     => ['required', 'integer'],
            'id_mat'    => ['nullable', 'integer'],
            'nota'      => ['required', 'integer'],
            'nota_seg'  => ['nullable', 'integer'],
            'paralelo'  => ['nullable', 'string', 'max:200'],
            'estado'    => ['nullable', 'integer'],
        ];
    }
}
