<?php

namespace App\Jobs;

use App\Mail\BirthdayInvitationMail;
use App\Models\BirthdayInvitationGuest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBirthdayInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(public int $guestId) {}

    public function handle(): void
    {
        $guest = BirthdayInvitationGuest::query()->with('invitation')->findOrFail($this->guestId);

        if (! $guest->invitation->is_published) {
            return;
        }

        Mail::to($guest->email)->sendNow(new BirthdayInvitationMail($guest->invitation, $guest));
        $guest->update(['sent_at' => now()]);
    }
}
