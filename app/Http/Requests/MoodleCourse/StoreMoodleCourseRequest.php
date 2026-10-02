<?php

namespace App\Http\Requests\MoodleCourse;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoodleCourseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fullname'   => ['required', 'string', 'max:255'],
            'shortname'  => ['required', 'string', 'max:100'],
            'categoryid' => ['sometimes', 'integer'],
            'summary'    => ['sometimes', 'string'],
        ];
    }
}
