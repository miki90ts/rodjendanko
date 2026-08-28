<?php

namespace App\Enums;

enum MessageMode: string
{
    case Random = 'random';
    case Selected = 'selected';
    case Custom = 'custom';
}
