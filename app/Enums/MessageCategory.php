<?php

namespace App\Enums;

enum MessageCategory: string
{
    case Friendly = 'friendly';
    case Family = 'family';
    case Partner = 'partner';
    case Colleague = 'colleague';
    case Business = 'business';
    case Funny = 'funny';
    case Emotional = 'emotional';
    case Short = 'short';
    case Universal = 'universal';

    public function label(): string
    {
        return match ($this) {
            self::Friendly => 'Prijateljska',
            self::Family => 'Porodična',
            self::Partner => 'Za partnera',
            self::Colleague => 'Za kolegu',
            self::Business => 'Poslovna',
            self::Funny => 'Smešna',
            self::Emotional => 'Emotivna',
            self::Short => 'Kratka',
            self::Universal => 'Univerzalna',
        };
    }
}
