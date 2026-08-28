<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Enums\NotificationChannel;
use App\Mail\BirthdayDeliveryMail;
use App\Models\BirthdayContact;
use App\Models\BirthdayDelivery;
use App\Models\BirthdayMessage;
use App\Models\EmailTemplate;
use App\Services\BirthdayMessageRenderer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BirthdayPreviewController extends Controller
{
    public function message(Request $request, BirthdayMessageRenderer $renderer)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:40'],
            'tone' => ['nullable', 'string', 'max:40'],
            'exclude' => ['nullable', 'integer'],
        ]);
        $query = BirthdayMessage::query()->availableTo($request->user()->id)->where('is_active', true);
        $query->when($data['category'] ?? null, fn ($q, $value) => $q->where('category', $value));
        $query->when($data['tone'] ?? null, fn ($q, $value) => $q->where('tone', $value));
        $query->when($data['exclude'] ?? null, fn ($q, $value) => $q->whereKeyNot($value));
        $message = $query->inRandomOrder()->firstOrFail();
        $contact = new BirthdayContact($data);

        return response()->json(['message' => $message, 'rendered' => $renderer->render($message->body, $contact)]);
    }

    public function email(Request $request, BirthdayMessageRenderer $renderer)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
            'template_id' => ['required', Rule::exists('email_templates', 'id')->where('is_active', true)],
        ]);
        $contact = new BirthdayContact($data);
        $delivery = new BirthdayDelivery([
            'type' => DeliveryType::Greeting,
            'channel' => NotificationChannel::Email,
            'status' => DeliveryStatus::Pending,
            'recipient' => 'preview@example.com',
            'subject' => 'Srećan rođendan, '.$contact->first_name.'! 🎉',
            'rendered_message' => $renderer->render($data['message'], $contact),
        ]);
        $delivery->setRelation('contact', $contact);
        $delivery->setRelation('emailTemplate', EmailTemplate::findOrFail($data['template_id']));

        return response((new BirthdayDeliveryMail($delivery))->render());
    }
}
