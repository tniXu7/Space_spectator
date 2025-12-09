<?php

namespace App\Http\Validators;

use Illuminate\Validation\Validator;

class IssDataValidator
{
    public static function validate(array $data): array
    {
        $rules = [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'altitude' => 'nullable|numeric|min:0',
            'velocity' => 'nullable|numeric|min:0',
        ];
        
        $messages = [
            'latitude.between' => 'Широта должна быть в диапазоне от -90 до 90',
            'longitude.between' => 'Долгота должна быть в диапазоне от -180 до 180',
            'altitude.min' => 'Высота не может быть отрицательной',
            'velocity.min' => 'Скорость не может быть отрицательной',
        ];
        
        return validator($data, $rules, $messages)->validate();
    }
}

