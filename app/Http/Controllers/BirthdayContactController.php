<?php

namespace App\Http\Controllers;

use App\Actions\CreateBirthdayContactAction;
use App\Actions\UpdateBirthdayContactAction;
use App\Http\Requests\StoreBirthdayContactRequest;
use App\Http\Requests\UpdateBirthdayContactRequest;
use App\Http\Resources\BirthdayContactResource;
use App\Models\BirthdayContact;
use App\Models\BirthdayMessage;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class BirthdayContactController extends Controller
{
    public function index(Request $request): Response
    {
        $contacts = $request->user()->birthdayContacts()->with(['user.birthdaySettings', 'settings', 'reminders'])
            ->when($request->string('search')->isNotEmpty(), function (Builder $query) use ($request): void {
                $term = '%'.$request->string('search')->toString().'%';
                $query->where(fn (Builder $q) => $q->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('nickname', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($request->filled('relationship'), fn (Builder $q) => $q->where('relationship', $request->string('relationship')))
            ->when($request->boolean('auto_send'), fn (Builder $q) => $q->whereHas('settings', fn (Builder $s) => $s->where('auto_send_enabled', true)))
            ->get()
            ->when($request->input('date_filter') === 'upcoming', fn ($items) => $items->filter(fn (BirthdayContact $contact) => $contact->nextBirthday()->diffInDays(now()->startOfDay()) <= 30))
            ->when($request->input('date_filter') === 'this_month', fn ($items) => $items->filter(fn (BirthdayContact $contact) => $contact->nextBirthday()->month === now()->month))
            ->sortBy(fn (BirthdayContact $contact) => $contact->nextBirthday()->timestamp)
            ->values();

        $page = max(1, $request->integer('page', 1));
        $paginator = new LengthAwarePaginator($contacts->forPage($page, 12), $contacts->count(), 12, $page, ['path' => $request->url(), 'query' => $request->query()]);

        $paginator->through(fn (BirthdayContact $contact) => (new BirthdayContactResource($contact))->resolve($request));

        return Inertia::render('birthdays/Index', [
            'contacts' => $paginator,
            'calendarContacts' => BirthdayContactResource::collection($contacts)->resolve($request),
            'filters' => $request->only(['search', 'relationship', 'auto_send', 'date_filter']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('birthdays/Create', $this->formData($request));
    }

    public function store(StoreBirthdayContactRequest $request, CreateBirthdayContactAction $action)
    {
        $action->execute($request->user(), $request->validated());

        return to_route('birthdays.index')->with('success', 'Rođendan je dodat.');
    }

    public function show(BirthdayContact $birthdayContact): Response
    {
        $this->authorize('view', $birthdayContact);

        return Inertia::render('birthdays/Show', ['contact' => new BirthdayContactResource($birthdayContact->load(['user.birthdaySettings', 'settings.emailTemplate', 'settings.selectedMessage', 'reminders', 'deliveries' => fn ($q) => $q->latest()->limit(20)]))]);
    }

    public function edit(Request $request, BirthdayContact $birthdayContact): Response
    {
        $this->authorize('update', $birthdayContact);

        return Inertia::render('birthdays/Edit', [...$this->formData($request), 'contact' => new BirthdayContactResource($birthdayContact->load(['user.birthdaySettings', 'settings', 'reminders']))]);
    }

    public function update(UpdateBirthdayContactRequest $request, BirthdayContact $birthdayContact, UpdateBirthdayContactAction $action)
    {
        $action->execute($birthdayContact, $request->validated());

        return to_route('birthdays.index')->with('success', 'Podaci su sačuvani.');
    }

    public function destroy(BirthdayContact $birthdayContact)
    {
        $this->authorize('delete', $birthdayContact);
        $birthdayContact->delete();

        return to_route('birthdays.index')->with('success', 'Kontakt je obrisan.');
    }

    private function formData(Request $request): array
    {
        $settings = $request->user()->birthdaySettings()->firstOrCreate([], []);

        return [
            'defaults' => $settings,
            'messages' => BirthdayMessage::query()->availableTo($request->user()->id)->where('is_active', true)->orderBy('category')->get(['id', 'name', 'category', 'tone', 'body', 'user_id']),
            'templates' => EmailTemplate::query()->where('is_active', true)->get(),
        ];
    }
}
