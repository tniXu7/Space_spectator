<?php

namespace App\Http\Validators;

class AstroDataValidator
{
    public static function validate(array $data): array
    {
        $rules = [
            'lat' => 'nullable|numeric|between:-90,90',
            'lon' => 'nullable|numeric|between:-180,180',
            'days' => 'nullable|integer|min:1|max:30',
        ];
        
        $messages = [
            'lat.between' => 'Широта должна быть в диапазоне от -90 до 90',
            'lon.between' => 'Долгота должна быть в диапазоне от -180 до 180',
            'days.max' => 'Количество дней не может быть больше 30',
            'days.min' => 'Количество дней должно быть больше 0',
        ];
        
        return validator($data, $rules, $messages)->validate();
    }
}

