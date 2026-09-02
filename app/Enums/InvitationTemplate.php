<?php

namespace App\Enums;

enum InvitationTemplate: string
{
    case Kids = 'kids';
    case Teen = 'teen';
    case Cheerful = 'cheerful';
    case Elegant = 'elegant';

    public function label(): string
    {
        return match ($this) {
            self::Kids => 'Dečja čarolija',
            self::Teen => 'Neonska žurka',
            self::Cheerful => 'Veselo slavlje',
            self::Elegant => 'Elegantno veče',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Kids => 'Pastelni baloni, pokloni i razigrane boje za najmlađe.',
            self::Teen => 'Savremen ljubičasto-plavi izgled za tinejdžersku žurku.',
            self::Cheerful => 'Šaren i vedar izgled koji odgovara svim godinama.',
            self::Elegant => 'Sveden izgled u teget, bordo i zlatnim tonovima.',
        };
    }

    public function backgroundPath(): string
    {
        return match ($this) {
            self::Elegant => '/images/invitations/elegant-party.png',
            default => '/images/invitations/kids-party.png',
        };
    }
}
