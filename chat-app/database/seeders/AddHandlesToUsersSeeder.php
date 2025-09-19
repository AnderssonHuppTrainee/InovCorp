<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddHandlesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::whereNull('handle')->get();

        foreach ($users as $user) {
            // Gerar handle baseado no nome
            $baseHandle = strtolower(trim($user->name));
            $baseHandle = preg_replace('/[^a-z0-9]/', '', $baseHandle);

            // Se o handle estiver vazio, usar parte do email
            if (empty($baseHandle)) {
                $emailParts = explode('@', $user->email);
                $baseHandle = preg_replace('/[^a-z0-9]/', '', strtolower($emailParts[0]));
            }

            // Se ainda estiver vazio, usar ID
            if (empty($baseHandle)) {
                $baseHandle = 'user' . $user->id;
            }

            // Garantir que o handle seja único
            $handle = $baseHandle;
            $counter = 1;
            while (\App\Models\User::where('handle', $handle)->exists()) {
                $handle = $baseHandle . $counter;
                $counter++;
            }

            $user->update(['handle' => $handle]);
        }
    }
}
