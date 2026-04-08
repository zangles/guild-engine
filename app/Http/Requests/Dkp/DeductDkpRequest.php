<?php

namespace App\Http\Requests\Dkp;

use Illuminate\Foundation\Http\FormRequest;

class DeductDkpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
