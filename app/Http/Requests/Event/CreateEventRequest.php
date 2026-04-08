<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'starts_at'     => ['required', 'date', 'after:now'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
