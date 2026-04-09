<?php

namespace App\Http\Requests\GuildRole;

use App\Enums\GuildPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuildRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'permission_slugs'   => ['required', 'array'],
            'permission_slugs.*' => ['string', Rule::in(array_column(GuildPermission::cases(), 'value'))],
        ];
    }
}
