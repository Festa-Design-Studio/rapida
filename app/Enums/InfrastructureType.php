<?php

namespace App\Enums;

enum InfrastructureType: string
{
    case Commercial = 'commercial';
    case Government = 'government';
    case Utility = 'utility';
    case Transport = 'transport';
    case Community = 'community';
    case PublicRecreation = 'public_recreation';
    case Other = 'other';
}
