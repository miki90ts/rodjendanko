<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Jobs\SendBirthdayDeliveryJob;
use App\Models\BirthdayDelivery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BirthdayDeliveryController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('deliveries/Index', [
            'deliveries' => $request->user()->birthdayDeliveries()->with(['contact:id,first_name,last_name,nickname', 'message:id,name', 'emailTemplate:id,name'])->latest('scheduled_for')->paginate(20)->withQueryString(),
        ]);
    }

    public function retry(BirthdayDelivery $birthdayDelivery)
    {
        $this->authorize('update', $birthdayDelivery);
        abort_unless($birthdayDelivery->status === DeliveryStatus::Failed, 422);
        $birthdayDelivery->update(['status' => DeliveryStatus::Pending, 'error' => null]);
        SendBirthdayDeliveryJob::dispatch($birthdayDelivery->id);

        return back()->with('success', 'Ponovno slanje je stavljeno u red.');
    }
}
