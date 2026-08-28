<?php

namespace App\Services;

use App\Contracts\BirthdayMessageChannel;
use App\Enums\NotificationChannel;
use App\Services\Channels\EmailBirthdayMessageChannel;

class BirthdayChannelManager
{
    public function __construct(private readonly EmailBirthdayMessageChannel $email) {}

    public function for(NotificationChannel $channel): BirthdayMessageChannel
    {
        return match ($channel) {
            NotificationChannel::Email => $this->email,
        };
    }
}
