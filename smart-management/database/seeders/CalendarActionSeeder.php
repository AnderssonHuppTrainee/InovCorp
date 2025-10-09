<?php

namespace Database\Seeders;

use App\Models\System\Calendar\CalendarAction;
use Illuminate\Database\Seeder;

class CalendarActionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            ['name' => 'Follow-up', 'description' => 'Acompanhamento de cliente', 'is_active' => true],
            ['name' => 'Proposta Comercial', 'description' => 'Apresentar proposta', 'is_active' => true],
            ['name' => 'Reunião de Projeto', 'description' => 'Reunião sobre projeto', 'is_active' => true],
            ['name' => 'Demo de Produto', 'description' => 'Demonstração de produto', 'is_active' => true],
            ['name' => 'Negociação', 'description' => 'Negociação comercial', 'is_active' => true],
            ['name' => 'Suporte Técnico', 'description' => 'Atendimento de suporte', 'is_active' => true],
            ['name' => 'Visita Comercial', 'description' => 'Visita a cliente', 'is_active' => true],
        ];

        foreach ($actions as $action) {
            CalendarAction::firstOrCreate(
                ['name' => $action['name']],
                $action
            );
        }
    }
}


