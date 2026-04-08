<?php

namespace App\Http\Requests\GuildMember;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'guild_role_id' => ['required', 'integer', 'exists:guild_roles,id'],
        ];
    }
}
