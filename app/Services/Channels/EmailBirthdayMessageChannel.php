<?php

namespace App\Services\Channels;

use App\Contracts\BirthdayMessageChannel;
use App\Mail\BirthdayDeliveryMail;
use App\Models\BirthdayDelivery;
use Illuminate\Support\Facades\Mail;

class EmailBirthdayMessageChannel implements BirthdayMessageChannel
{
    public function send(BirthdayDelivery $delivery): ?string
    {
        Mail::to($delivery->recipient)->send(new BirthdayDeliveryMail($delivery));

        return null;
    }
}
