<?php

namespace App\Http\Validators;

class JwstDataValidator
{
    public static function validate(array $data): array
    {
        $rules = [
            'source' => 'nullable|in:jpg,suffix,program',
            'suffix' => 'nullable|string|max:50',
            'program' => 'nullable|string|max:20',
            'instrument' => 'nullable|in:NIRCam,MIRI,NIRISS,NIRSpec,FGS',
            'page' => 'nullable|integer|min:1|max:100',
            'perPage' => 'nullable|integer|min:1|max:60',
        ];
        
        $messages = [
            'source.in' => 'Источник должен быть одним из: jpg, suffix, program',
            'instrument.in' => 'Инструмент должен быть одним из: NIRCam, MIRI, NIRISS, NIRSpec, FGS',
            'page.max' => 'Страница не может быть больше 100',
            'perPage.max' => 'Количество элементов на странице не может быть больше 60',
        ];
        
        return validator($data, $rules, $messages)->validate();
    }
}

