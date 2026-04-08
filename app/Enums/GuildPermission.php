<?php

namespace App\Enums;

enum GuildPermission: string
{
    case ManageEvents       = 'manage_events';
    case ApproveMembers     = 'approve_members';
    case InviteMembers      = 'invite_members';
    case KickMembers        = 'kick_members';
    case ManageDkp          = 'manage_dkp';
    case ManageDonations    = 'manage_donations';
    case RegisterAttendance = 'register_attendance';
    case ViewAuditLog       = 'view_audit_log';
    case ManageRoles        = 'manage_roles';
    case TransferLeadership = 'transfer_leadership';
}
