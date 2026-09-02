<?php

namespace App\Http\Controllers;

use App\Enums\RsvpStatus;
use App\Models\BirthdayInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PublicBirthdayInvitationController extends Controller
{
    public function show(Request $request, string $publicToken): Response
    {
        $invitation = BirthdayInvitation::query()
            ->where('public_token', $publicToken)
            ->where('is_published', true)
            ->firstOrFail();
        $guest = $request->filled('guest')
            ? $invitation->guests()->where('token', $request->string('guest'))->first()
            : null;

        return Inertia::render('invitations/Public', [
            'invitation' => [
                ...$invitation->only([
                    'id',
                    'public_token',
                    'title',
                    'honoree_name',
                    'celebrant_age',
                    'starts_at',
                    'ends_at',
                    'timezone',
                    'venue_name',
                    'address',
                    'city',
                    'latitude',
                    'longitude',
                    'description',
                    'dress_code',
                    'gift_note',
                    'host_name',
                    'host_phone',
                    'reply_deadline',
                    'allow_plus_one',
                    'max_guests',
                ]),
                'template' => $invitation->template->value,
                'background_url' => $invitation->background_image_path
                    ? Storage::disk('public')->url($invitation->background_image_path)
                    : asset($invitation->template->backgroundPath()),
            ],
            'guest' => $guest?->only(['token', 'name', 'email', 'status', 'party_size', 'message']),
            'initialResponse' => in_array($request->input('answer'), [RsvpStatus::Attending->value, RsvpStatus::NotAttending->value], true)
                ? $request->input('answer')
                : null,
        ]);
    }

    public function respond(Request $request, string $publicToken, string $guestToken): RedirectResponse
    {
        $invitation = BirthdayInvitation::query()
            ->where('public_token', $publicToken)
            ->where('is_published', true)
            ->firstOrFail();
        $guest = $invitation->guests()->where('token', $guestToken)->firstOrFail();

        if ($invitation->reply_deadline && now($invitation->timezone)->startOfDay()->isAfter($invitation->reply_deadline)) {
            return back()->withErrors(['status' => 'Rok za potvrdu dolaska je istekao. Obratite se domaćinu.']);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in([RsvpStatus::Attending->value, RsvpStatus::NotAttending->value])],
            'party_size' => ['required', 'integer', 'min:1', 'max:'.($invitation->allow_plus_one ? 2 : 1)],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === RsvpStatus::Attending->value && $invitation->max_guests) {
            $alreadyConfirmed = (int) $invitation->guests()
                ->where('status', RsvpStatus::Attending->value)
                ->whereKeyNot($guest->getKey())
                ->sum('party_size');

            if ($alreadyConfirmed + $data['party_size'] > $invitation->max_guests) {
                return back()->withErrors([
                    'party_size' => 'Nažalost, nema dovoljno slobodnih mesta za izabrani broj osoba. Obratite se domaćinu.',
                ])->withInput();
            }
        }

        $guest->update([
            ...$data,
            'party_size' => $data['status'] === RsvpStatus::Attending->value ? $data['party_size'] : 1,
            'responded_at' => now(),
        ]);

        return back()->with('success', $data['status'] === RsvpStatus::Attending->value
            ? 'Hvala! Dolazak je potvrđen.'
            : 'Odgovor je sačuvan. Nedostajaćete nam!');
    }
}
