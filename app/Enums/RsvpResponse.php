<?php

namespace App\Enums;

enum RsvpResponse: string
{
    case Confirmed = 'confirmed';
    case Declined  = 'declined';
    case Tentative = 'tentative';
}
