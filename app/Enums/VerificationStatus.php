<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case Pending = 'pending';
    case InField = 'in_field';
    case Verified = 'verified';
    case Disputed = 'disputed';
}
