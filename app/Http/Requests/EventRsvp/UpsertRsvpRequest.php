<?php

namespace App\Http\Requests\EventRsvp;

use App\Enums\RsvpResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertRsvpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'response' => ['required', Rule::enum(RsvpResponse::class)],
        ];
    }
}
