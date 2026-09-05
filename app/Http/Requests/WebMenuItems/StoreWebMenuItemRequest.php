<?php

namespace App\Http\Requests\WebMenuItems;

use Illuminate\Foundation\Http\FormRequest;

class StoreWebMenuItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'menu_id'   => 'required|integer',
            'label'     => 'required|string|max:255',
            'url'       => 'required|string|max:500',
            'orden'     => 'nullable|integer',
            'target'    => 'nullable|string|max:20',
            'activo'    => 'nullable|boolean',
        ];
    }
}
