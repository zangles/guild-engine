<?php

namespace App\Http\Requests\Guild;

use Illuminate\Foundation\Http\FormRequest;

class CreateGuildRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'game'        => ['required', 'string', 'max:255'],
            'is_public'   => ['boolean'],
        ];
    }
}
