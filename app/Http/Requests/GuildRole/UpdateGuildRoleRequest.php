<?php

namespace App\Http\Requests\GuildRole;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuildRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'permission_ids'   => ['required', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }
}
