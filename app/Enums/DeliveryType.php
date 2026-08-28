<?php

namespace App\Enums;

enum DeliveryType: string
{
    case Reminder = 'reminder';
    case Greeting = 'greeting';
}
