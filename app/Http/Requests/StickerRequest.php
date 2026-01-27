<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StickerRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $stickerId = $this->route('id');

        return [
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('stickers', 'number')->ignore($stickerId),
            ],
            'name' => 'required|string|max:255',
            'page_number' => 'required|integer|min:1',
            'position_x' => 'required|integer|min:0',
            'position_y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'is_horizontal' => 'boolean',
            'rarity' => 'required|in:common,shiny',
            'image_path' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'number' => 'número',
            'name' => 'nombre',
            'page_number' => 'número de página',
            'position_x' => 'posición X',
            'position_y' => 'posición Y',
            'width' => 'ancho',
            'height' => 'alto',
            'is_horizontal' => 'horizontal',
            'rarity' => 'rareza',
            'image_path' => 'imagen',
        ];
    }

    public function messages()
    {
        return [
            'number.unique' => 'Ya existe un cromo con este número.',
        ];
    }
}
