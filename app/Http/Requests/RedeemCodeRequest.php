<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RedeemCodeRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('redeem_codes', 'code')->ignore($id),
            ],
            'packs_count' => ['required', 'integer', 'min:1'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
            'user_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'código',
            'packs_count' => 'número de sobres',
            'max_redemptions' => 'máximo de canjes',
            'expires_at' => 'fecha de expiración',
            'user_id' => 'usuario asignado',
            'is_active' => 'activo',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'El código es obligatorio.',
            'code.unique' => 'Este código ya existe.',
            'packs_count.required' => 'El número de sobres es obligatorio.',
            'packs_count.min' => 'Debe otorgar al menos 1 sobre.',
        ];
    }
}
