<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBirthdaySettingsRequest;
use App\Models\EmailTemplate;
use DateTimeZone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BirthdaySettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Birthdays', [
            'settings' => $request->user()->birthdaySettings()->firstOrCreate([], []),
            'templates' => EmailTemplate::query()->where('is_active', true)->get(),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(UpdateBirthdaySettingsRequest $request)
    {
        $request->user()->birthdaySettings()->updateOrCreate([], $request->validated());

        return back()->with('success', 'Podešavanja su sačuvana.');
    }
}
