<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBirthdayMessageRequest;
use App\Models\BirthdayMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BirthdayMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $messages = BirthdayMessage::query()->availableTo($request->user()->id)
            ->when($request->filled('search'), fn ($q) => $q->where('body', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->filled('tone'), fn ($q) => $q->where('tone', $request->string('tone')))
            ->when($request->input('source') === 'system', fn ($q) => $q->whereNull('user_id'))
            ->when($request->input('source') === 'custom', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest('user_id')->paginate(18)->withQueryString();

        return Inertia::render('messages/Index', ['messages' => $messages, 'filters' => $request->only(['search', 'category', 'tone', 'source'])]);
    }

    public function store(StoreBirthdayMessageRequest $request)
    {
        $request->user()->birthdayMessages()->create([...$request->validated(), 'is_active' => true]);

        return back()->with('success', 'Poruka je dodata.');
    }

    public function update(StoreBirthdayMessageRequest $request, BirthdayMessage $birthdayMessage)
    {
        $this->authorize('update', $birthdayMessage);
        $birthdayMessage->update($request->validated());

        return back()->with('success', 'Poruka je sačuvana.');
    }

    public function destroy(BirthdayMessage $birthdayMessage)
    {
        $this->authorize('delete', $birthdayMessage);
        $birthdayMessage->delete();

        return back()->with('success', 'Poruka je obrisana.');
    }
}
