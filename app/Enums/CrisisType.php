<?php

namespace App\Enums;

enum CrisisType: string
{
    case Earthquake = 'earthquake';
    case Flood = 'flood';
    case Tsunami = 'tsunami';
    case Hurricane = 'hurricane';
    case Wildfire = 'wildfire';
    case Explosion = 'explosion';
    case Chemical = 'chemical';
    case Conflict = 'conflict';
    case CivilUnrest = 'civil_unrest';
}
