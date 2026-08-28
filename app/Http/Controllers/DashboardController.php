<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Http\Resources\BirthdayContactResource;
use App\Models\BirthdayContact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $query = $request->user()->birthdayContacts()->where('is_active', true)->with(['user.birthdaySettings', 'settings', 'reminders']);
        $contacts = $query->get()->sortBy(fn (BirthdayContact $contact) => $contact->nextBirthday()->timestamp)->values();
        $today = $contacts->filter(fn (BirthdayContact $contact) => $contact->nextBirthday()->isToday());
        $upcoming = $contacts->filter(fn (BirthdayContact $contact) => $contact->nextBirthday()->diffInDays(now()->startOfDay()) <= 30);

        return Inertia::render('Dashboard', [
            'today' => BirthdayContactResource::collection($today)->resolve(),
            'upcoming' => BirthdayContactResource::collection($upcoming->take(4))->resolve(),
            'nextBirthday' => $contacts->first() ? (new BirthdayContactResource($contacts->first()))->resolve() : null,
            'stats' => [
                'contacts' => $contacts->count(),
                'this_month' => $contacts->filter(fn (BirthdayContact $c) => $c->nextBirthday()->month === now()->month)->count(),
                'scheduled' => $request->user()->birthdayDeliveries()->where('type', DeliveryType::Greeting->value)->whereIn('status', [DeliveryStatus::Pending->value, DeliveryStatus::Processing->value])->count(),
                'sent' => $request->user()->birthdayDeliveries()->where('status', DeliveryStatus::Sent->value)->count(),
            ],
        ]);
    }
}
