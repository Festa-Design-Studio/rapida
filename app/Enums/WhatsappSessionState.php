<?php

namespace App\Enums;

enum WhatsappSessionState: string
{
    case AwaitingPhoto = 'AWAITING_PHOTO';
    case AwaitingLocation = 'AWAITING_LOCATION';
    case AwaitingDamage = 'AWAITING_DAMAGE';
    case AwaitingInfra = 'AWAITING_INFRA';
    case AwaitingConfirm = 'AWAITING_CONFIRM';
    case Submitted = 'SUBMITTED';
    case Expired = 'EXPIRED';
}
