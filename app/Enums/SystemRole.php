<?php

namespace App\Enums;

enum SystemRole: string
{
    case Leader   = 'Líder';
    case Official = 'Oficial';
    case Member   = 'Miembro';

    /** @return GuildPermission[] */
    public function permissions(): array
    {
        return match($this) {
            self::Leader   => GuildPermission::cases(),
            self::Official => [
                GuildPermission::ManageEvents,
                GuildPermission::ApproveMembers,
                GuildPermission::InviteMembers,
                GuildPermission::KickMembers,
                GuildPermission::ManageDkp,
                GuildPermission::ManageDonations,
                GuildPermission::RegisterAttendance,
                GuildPermission::ViewAuditLog,
                GuildPermission::IsGuildMember,
            ],
            self::Member   => [
                GuildPermission::IsGuildMember,
            ],
        };
    }
}