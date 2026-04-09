<?php

namespace App\Enums;

enum UndpUserRole: string
{
    case FieldCoordinator = 'field_coordinator';
    case Analyst = 'analyst';
    case Operator = 'operator';
    case Superadmin = 'superadmin';
}
