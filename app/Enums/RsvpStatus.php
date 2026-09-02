<?php

namespace App\Enums;

enum RsvpStatus: string
{
    case Pending = 'pending';
    case Attending = 'attending';
    case NotAttending = 'not_attending';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Čeka odgovor',
            self::Attending => 'Dolazi',
            self::NotAttending => 'Ne dolazi',
        };
    }
}
