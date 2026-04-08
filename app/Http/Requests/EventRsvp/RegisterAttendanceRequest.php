<?php

namespace App\Http\Requests\EventRsvp;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'attendances'          => ['required', 'array'],
            'attendances.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'attendances.*.attended' => ['required', 'boolean'],
        ];
    }
}
