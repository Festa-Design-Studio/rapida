<?php

namespace App\Enums;

enum LocationMethod: string
{
    case FootprintTap = 'footprint_tap';
    case GpsSnap = 'gps_snap';
    case W3w = 'w3w';
    case LandmarkPicker = 'landmark_picker';
    case LandmarkText = 'landmark_text';
    case WhatsappPin = 'whatsapp_pin';
    case CoordinateOnly = 'coordinate_only';
}
