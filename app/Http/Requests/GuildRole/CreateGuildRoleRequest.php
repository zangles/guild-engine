<?php

namespace App\Http\Requests\GuildRole;

use App\Enums\GuildPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGuildRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:100'],
            'permission_slugs'   => ['array'],
            'permission_slugs.*' => ['string', Rule::in(array_column(GuildPermission::cases(), 'value'))],
        ];
    }
}
