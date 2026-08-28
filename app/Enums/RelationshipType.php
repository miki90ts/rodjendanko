<?php

namespace App\Enums;

enum RelationshipType: string
{
    case Family = 'family';
    case Friend = 'friend';
    case Partner = 'partner';
    case Colleague = 'colleague';
    case Business = 'business';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Family => 'Porodica',
            self::Friend => 'Prijatelj',
            self::Partner => 'Partner',
            self::Colleague => 'Kolega',
            self::Business => 'Poslovni kontakt',
            self::Other => 'Ostalo',
        };
    }
}
