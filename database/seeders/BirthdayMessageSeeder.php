<?php

namespace Database\Seeders;

use App\Models\BirthdayMessage;
use Illuminate\Database\Seeder;
use RuntimeException;

class BirthdayMessageSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('data/birthday_messages.json')), true, flags: JSON_THROW_ON_ERROR);
        $messages = [];

        foreach ($data as $category => $group) {
            foreach ($group['openings'] as $opening) {
                foreach ($group['wishes'] as $wish) {
                    foreach ($group['closings'] as $closing) {
                        $messages[] = [
                            'user_id' => null,
                            'name' => ucfirst($category).' '.(count($messages) + 1),
                            'category' => $category,
                            'tone' => $group['tone'],
                            'body' => "$opening $wish $closing",
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        if (count($messages) < 200) {
            throw new RuntimeException('Birthday message library must contain at least 200 messages.');
        }

        BirthdayMessage::query()->whereNull('user_id')->delete();
        BirthdayMessage::query()->insert($messages);
    }
}
