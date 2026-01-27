<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $pageId = $this->route('id');

        return [
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('pages', 'number')->ignore($pageId),
            ],
            'image_path' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'number' => 'número de página',
            'image_path' => 'imagen',
        ];
    }

    public function messages()
    {
        return [
            'number.unique' => 'Ya existe una página con este número.',
        ];
    }
}
