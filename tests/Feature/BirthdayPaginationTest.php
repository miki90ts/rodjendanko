<?php

namespace Tests\Feature;

use App\Models\BirthdayContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BirthdayPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_birthday_cards_are_paginated_while_calendar_receives_all_contacts(): void
    {
        $user = User::factory()->create();
        BirthdayContact::factory()->count(13)->for($user)->create();

        $this->actingAs($user)->get(route('birthdays.index'))->assertInertia(
            fn (Assert $page) => $page
                ->component('birthdays/Index')
                ->has('contacts.data', 12)
                ->where('contacts.total', 13)
                ->where('contacts.current_page', 1)
                ->has('contacts.links')
                ->has('calendarContacts', 13),
        );

        $this->actingAs($user)->get(route('birthdays.index', ['page' => 2]))->assertInertia(
            fn (Assert $page) => $page
                ->has('contacts.data', 1)
                ->where('contacts.current_page', 2)
                ->has('calendarContacts', 13),
        );
    }

    public function test_message_library_exposes_pagination_links(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 19) as $index) {
            $user->birthdayMessages()->create([
                'name' => "Poruka {$index}",
                'category' => 'universal',
                'tone' => 'warm',
                'body' => "Srećan rođendan {$index}!",
                'is_active' => true,
            ]);
        }

        $this->actingAs($user)->get(route('messages.index'))->assertInertia(
            fn (Assert $page) => $page
                ->has('messages.data', 18)
                ->where('messages.total', 19)
                ->where('messages.last_page', 2)
                ->has('messages.links'),
        );
    }

    public function test_delivery_history_exposes_pagination_links(): void
    {
        $user = User::factory()->create();
        $contact = BirthdayContact::factory()->for($user)->create();

        foreach (range(1, 21) as $index) {
            $contact->deliveries()->create([
                'idempotency_key' => (string) Str::uuid(),
                'user_id' => $user->id,
                'type' => 'greeting',
                'channel' => 'email',
                'birthday_year' => 2026,
                'occurrence_key' => "pagination-{$index}",
                'scheduled_for' => now()->addMinutes($index),
                'status' => 'pending',
                'recipient' => 'test@example.com',
            ]);
        }

        $this->actingAs($user)->get(route('deliveries.index'))->assertInertia(
            fn (Assert $page) => $page
                ->has('deliveries.data', 20)
                ->where('deliveries.total', 21)
                ->where('deliveries.last_page', 2)
                ->has('deliveries.links'),
        );
    }
}
