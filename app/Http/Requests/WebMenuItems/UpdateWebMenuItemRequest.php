<?php

namespace App\Http\Requests\WebMenuItems;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebMenuItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'menu_id' => 'nullable|integer',
            'label'   => 'nullable|string|max:255',
            'url'     => 'nullable|string|max:500',
            'orden'   => 'nullable|integer',
            'target'  => 'nullable|string|max:20',
            'activo'  => 'nullable|boolean',
        ];
    }
}
