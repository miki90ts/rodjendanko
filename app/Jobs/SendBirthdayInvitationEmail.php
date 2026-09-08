<?php

namespace App\Jobs;

use App\Mail\BirthdayInvitationMail;
use App\Models\BirthdayInvitationGuest;
use App\Models\BirthdayInvitationSendBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBirthdayInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public int $timeout = 60;

    public function __construct(public int $guestId, public int $batchId) {}

    public function handle(): void
    {
        $guest = BirthdayInvitationGuest::query()->with('invitation')->find($this->guestId);

        if (! $guest || ! $guest->invitation->is_published) {
            $this->recordResult(false);

            return;
        }

        Mail::to($guest->email)->sendNow(new BirthdayInvitationMail($guest->invitation, $guest));
        $this->recordResult(true, $guest);
    }

    public function failed(?Throwable $exception): void
    {
        $this->recordResult(false);
    }

    private function recordResult(bool $successful, ?BirthdayInvitationGuest $guest = null): void
    {
        DB::transaction(function () use ($successful, $guest): void {
            $batch = BirthdayInvitationSendBatch::query()->lockForUpdate()->find($this->batchId);

            if (! $batch || $batch->completed_at) {
                return;
            }

            if ($successful && $guest) {
                $guest->increment('send_count');
                $guest->update(['sent_at' => now()]);
                $batch->successful_count++;
            } else {
                $batch->failed_count++;
            }

            if ($batch->successful_count + $batch->failed_count >= $batch->recipient_count) {
                $batch->completed_at = now();
                $batch->status = match (true) {
                    $batch->successful_count === $batch->recipient_count => 'completed',
                    $batch->successful_count > 0 => 'partial',
                    default => 'failed',
                };
            }

            $batch->save();
        });
    }
}
