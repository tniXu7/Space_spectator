<?php

namespace App\Http\Validators;

class OsdrDataValidator
{
    public static function validate(array $data): array
    {
        $rules = [
            'limit' => 'nullable|integer|min:1|max:100',
            'dataset_id' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
        ];
        
        $messages = [
            'limit.max' => 'Лимит не может быть больше 100',
            'limit.min' => 'Лимит должен быть больше 0',
        ];
        
        return validator($data, $rules, $messages)->validate();
    }
}

