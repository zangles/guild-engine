<?php

namespace App\Enums;

enum GuildMemberStatus: string
{
    case Active         = 'active';
    case PendingRequest = 'pending_request';
    case PendingInvite  = 'pending_invite';
    case Rejected       = 'rejected';
    case Kicked         = 'kicked';
    case Left           = 'left';
}
