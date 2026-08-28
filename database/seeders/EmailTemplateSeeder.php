<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['key' => 'minimal', 'name' => 'Minimal', 'description' => 'Čist i nenametljiv dizajn', 'view' => 'mail.birthdays.minimal', 'accent_color' => '#111827'],
            ['key' => 'elegant', 'name' => 'Elegant', 'description' => 'Prefinjen izgled sa zlatnim detaljima', 'view' => 'mail.birthdays.elegant', 'accent_color' => '#a16207'],
            ['key' => 'celebration', 'name' => 'Celebration', 'description' => 'Vedra rođendanska proslava', 'view' => 'mail.birthdays.celebration', 'accent_color' => '#7c3aed'],
            ['key' => 'warm', 'name' => 'Warm', 'description' => 'Topao i ličan izgled', 'view' => 'mail.birthdays.warm', 'accent_color' => '#e11d48'],
            ['key' => 'colorful', 'name' => 'Colorful', 'description' => 'Razigrane boje za vesele čestitke', 'view' => 'mail.birthdays.colorful', 'accent_color' => '#0891b2'],
        ] as $template) {
            EmailTemplate::query()->updateOrCreate(['key' => $template['key']], [...$template, 'is_active' => true]);
        }
    }
}
