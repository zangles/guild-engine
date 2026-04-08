<?php

namespace App\Http\Requests\GuildRole;

use Illuminate\Foundation\Http\FormRequest;

class CreateGuildRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }
}
