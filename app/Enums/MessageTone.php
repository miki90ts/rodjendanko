<?php

namespace App\Enums;

enum MessageTone: string
{
    case Warm = 'warm';
    case Funny = 'funny';
    case Formal = 'formal';
    case Emotional = 'emotional';
    case Short = 'short';
    case Casual = 'casual';

    public function label(): string
    {
        return match ($this) {
            self::Warm => 'Topao',
            self::Funny => 'Duhovit',
            self::Formal => 'Formalan',
            self::Emotional => 'Emotivan',
            self::Short => 'Kratak',
            self::Casual => 'Opušten',
        };
    }
}
