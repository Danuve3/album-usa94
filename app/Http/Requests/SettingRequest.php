<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $settingId = $this->route('id');
        $setting = Setting::find($settingId);

        if (! $setting) {
            return ['value' => ['required']];
        }

        return match ($setting->key) {
            'shiny_probability' => [
                'value' => ['required', 'numeric', 'min:0', 'max:1'],
            ],
            'stickers_per_pack' => [
                'value' => ['required', 'integer', 'min:1', 'max:20'],
            ],
            'pack_delivery_interval_minutes' => [
                'value' => ['required', 'integer', 'min:1', 'max:1440'],
            ],
            'packs_per_delivery' => [
                'value' => ['required', 'integer', 'min:1', 'max:10'],
            ],
            default => [
                'value' => ['required'],
            ],
        };
    }

    public function attributes()
    {
        return [
            'value' => 'valor',
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'El valor es obligatorio.',
            'value.numeric' => 'El valor debe ser numérico.',
            'value.integer' => 'El valor debe ser un número entero.',
            'value.min' => 'El valor mínimo es :min.',
            'value.max' => 'El valor máximo es :max.',
        ];
    }
}
