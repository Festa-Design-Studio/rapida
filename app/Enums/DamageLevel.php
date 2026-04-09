<?php

namespace App\Enums;

enum DamageLevel: string
{
    case Minimal = 'minimal';
    case Partial = 'partial';
    case Complete = 'complete';
}
