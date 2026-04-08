<?php

namespace App\Http\Requests\Guild;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuildRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'                           => ['required', 'string', 'max:255'],
            'description'                    => ['nullable', 'string'],
            'game'                           => ['required', 'string', 'max:255'],
            'is_public'                      => ['boolean'],
            'dkp_currency_name'              => ['required', 'string', 'max:50'],
            'discord_webhook_url'            => ['nullable', 'url'],
            'discord_advance_notice_minutes' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
