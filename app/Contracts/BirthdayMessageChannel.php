<?php

namespace App\Contracts;

use App\Models\BirthdayDelivery;

interface BirthdayMessageChannel
{
    public function send(BirthdayDelivery $delivery): ?string;
}
