<?php

namespace App\Mail;

use App\Enums\InvitationTemplate;
use App\Models\BirthdayInvitation;
use App\Models\BirthdayInvitationGuest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BirthdayInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public BirthdayInvitation $invitation,
        public BirthdayInvitationGuest $guest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Pozivnica: {$this->invitation->title}");
    }

    public function content(): Content
    {
        $template = $this->invitation->template instanceof InvitationTemplate
            ? $this->invitation->template
            : InvitationTemplate::from($this->invitation->template);
        $backgroundUrl = $this->invitation->background_image_path
            ? Storage::disk('public')->url($this->invitation->background_image_path)
            : asset($template->backgroundPath());

        return new Content(view: 'mail.invitations.invitation', with: [
            'invitation' => $this->invitation,
            'guest' => $this->guest,
            'backgroundUrl' => $backgroundUrl,
            'attendingUrl' => route('invitations.public', [
                'publicToken' => $this->invitation->public_token,
                'guest' => $this->guest->token,
                'answer' => 'attending',
            ]).'#rsvp',
            'decliningUrl' => route('invitations.public', [
                'publicToken' => $this->invitation->public_token,
                'guest' => $this->guest->token,
                'answer' => 'not_attending',
            ]).'#rsvp',
            'mapUrl' => 'https://www.google.com/maps/search/?api=1&query='.urlencode(collect([
                $this->invitation->venue_name,
                $this->invitation->address,
                $this->invitation->city,
            ])->filter()->implode(', ')),
        ]);
    }
}
