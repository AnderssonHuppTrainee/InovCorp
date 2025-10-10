<?php

namespace Database\Seeders;

use App\Models\System\Calendar\CalendarEventType;
use Illuminate\Database\Seeder;

class CalendarEventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Reunião', 'color' => '#3b82f6', 'is_active' => true],
            ['name' => 'Chamada', 'color' => '#10b981', 'is_active' => true],
            ['name' => 'Visita', 'color' => '#f59e0b', 'is_active' => true],
            ['name' => 'Apresentação', 'color' => '#8b5cf6', 'is_active' => true],
            ['name' => 'Formação', 'color' => '#ec4899', 'is_active' => true],
            ['name' => 'Outro', 'color' => '#6b7280', 'is_active' => true],
        ];

        foreach ($types as $type) {
            CalendarEventType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}



