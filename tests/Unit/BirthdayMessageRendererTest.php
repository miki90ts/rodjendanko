<?php

namespace Tests\Unit;

use App\Models\BirthdayContact;
use App\Services\BirthdayMessageRenderer;
use PHPUnit\Framework\TestCase;

class BirthdayMessageRendererTest extends TestCase
{
    public function test_it_personalizes_all_supported_placeholders(): void
    {
        $contact = new BirthdayContact(['first_name' => 'Marko', 'last_name' => 'Marković', 'nickname' => 'Mare']);
        $rendered = (new BirthdayMessageRenderer)->render('Ćao {{nickname}} ({{first_name}} {{last_name}})!', $contact);
        $this->assertSame('Ćao Mare (Marko Marković)!', $rendered);
    }

    public function test_it_falls_back_to_first_name_for_a_missing_nickname(): void
    {
        $contact = new BirthdayContact(['first_name' => 'Ana']);
        $this->assertSame('Srećno, Ana!', (new BirthdayMessageRenderer)->render('Srećno, {{nickname}}!', $contact));
    }
}
