<?php

namespace App\Http\Controllers;

use App\Enums\InvitationTemplate;
use App\Http\Requests\StoreBirthdayInvitationRequest;
use App\Jobs\SendBirthdayInvitationEmail;
use App\Models\BirthdayInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BirthdayInvitationController extends Controller
{
    public function index(Request $request): Response
    {
        $invitations = $request->user()->birthdayInvitations()
            ->withCount([
                'guests',
                'guests as attending_count' => fn ($query) => $query->where('status', 'attending'),
                'guests as pending_count' => fn ($query) => $query->where('status', 'pending'),
            ])
            ->orderByDesc('starts_at')
            ->paginate(12)
            ->withQueryString();

        $invitations->through(fn (BirthdayInvitation $invitation) => $this->invitationData($invitation));

        return Inertia::render('invitations/Index', ['invitations' => $invitations]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('invitations/Create', $this->formData($request));
    }

    public function store(StoreBirthdayInvitationRequest $request): RedirectResponse
    {
        $invitation = DB::transaction(function () use ($request): BirthdayInvitation {
            $data = $request->validated();
            $guests = Arr::pull($data, 'guests');
            $sendNow = Arr::pull($data, 'send_now');
            unset($data['background_image']);

            if ($request->hasFile('background_image')) {
                $data['background_image_path'] = $request->file('background_image')->store("invitations/{$request->user()->id}", 'public');
            }

            $invitation = $request->user()->birthdayInvitations()->create([
                ...$data,
                'public_token' => (string) Str::uuid(),
            ]);

            $invitation->guests()->createMany(collect($guests)->map(fn (array $guest) => [
                ...$guest,
                'token' => (string) Str::uuid(),
            ])->all());

            $invitation->setAttribute('send_now', $sendNow);

            return $invitation;
        });

        if ($invitation->getAttribute('send_now')) {
            $this->queueInvitations($invitation);
        }

        return to_route('invitations.show', $invitation)->with('success', 'Pozivnica je kreirana.');
    }

    public function show(BirthdayInvitation $invitation): Response
    {
        $this->authorize('view', $invitation);
        $invitation->load(['guests' => fn ($query) => $query->orderBy('status')->orderBy('name')->orderBy('email')]);

        return Inertia::render('invitations/Show', [
            'invitation' => $this->invitationData($invitation),
        ]);
    }

    public function edit(Request $request, BirthdayInvitation $invitation): Response
    {
        $this->authorize('update', $invitation);
        $invitation->load('guests');

        return Inertia::render('invitations/Edit', [
            ...$this->formData($request),
            'invitation' => $this->invitationData($invitation),
        ]);
    }

    public function update(StoreBirthdayInvitationRequest $request, BirthdayInvitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        DB::transaction(function () use ($request, $invitation): void {
            $data = $request->validated();
            $guests = Arr::pull($data, 'guests');
            Arr::forget($data, ['send_now', 'background_image']);

            if ($request->hasFile('background_image')) {
                $data['background_image_path'] = $request->file('background_image')->store("invitations/{$request->user()->id}", 'public');
            }

            $invitation->update($data);
            $emails = [];

            foreach ($guests as $guestData) {
                $emails[] = $guestData['email'];
                $guest = $invitation->guests()->firstOrNew(['email' => $guestData['email']]);
                $guest->fill($guestData);

                if (! $guest->exists) {
                    $guest->token = (string) Str::uuid();
                }

                $guest->save();
            }

            $invitation->guests()->whereNotIn('email', $emails)->delete();
        });

        if ($request->boolean('send_now')) {
            $this->queueInvitations($invitation);
        }

        return to_route('invitations.show', $invitation)->with('success', 'Pozivnica je sačuvana.');
    }

    public function destroy(BirthdayInvitation $invitation): RedirectResponse
    {
        $this->authorize('delete', $invitation);

        if ($invitation->background_image_path) {
            Storage::disk('public')->delete($invitation->background_image_path);
        }

        $invitation->delete();

        return to_route('invitations.index')->with('success', 'Pozivnica je obrisana.');
    }

    public function send(BirthdayInvitation $invitation): RedirectResponse
    {
        $this->authorize('update', $invitation);

        if (! $invitation->is_published) {
            return back()->withErrors(['send' => 'Najpre aktivirajte javnu stranicu pozivnice, pa zatim pošaljite emailove.']);
        }

        $count = $this->queueInvitations($invitation);

        return back()->with('success', "Pozivnice su stavljene u red za {$count} gostiju.");
    }

    private function queueInvitations(BirthdayInvitation $invitation): int
    {
        $invitation->loadMissing('guests');

        foreach ($invitation->guests as $guest) {
            SendBirthdayInvitationEmail::dispatch($guest->id);
        }

        return $invitation->guests->count();
    }

    private function formData(Request $request): array
    {
        return [
            'templates' => collect(InvitationTemplate::cases())->map(fn (InvitationTemplate $template) => [
                'value' => $template->value,
                'label' => $template->label(),
                'description' => $template->description(),
                'background_url' => asset($template->backgroundPath()),
            ]),
            'timezones' => ['Europe/Belgrade', 'Europe/Budapest', 'Europe/Vienna', 'Europe/Berlin', 'Europe/Zurich', 'UTC'],
            'defaults' => [
                'host_name' => $request->user()->name,
                'timezone' => $request->user()->birthdaySettings?->timezone ?? 'Europe/Belgrade',
            ],
        ];
    }

    private function invitationData(BirthdayInvitation $invitation): array
    {
        return [
            ...$invitation->toArray(),
            'template' => $invitation->template->value,
            'background_url' => $invitation->background_image_path
                ? Storage::disk('public')->url($invitation->background_image_path)
                : asset($invitation->template->backgroundPath()),
            'public_url' => route('invitations.public', $invitation->public_token),
        ];
    }
}
