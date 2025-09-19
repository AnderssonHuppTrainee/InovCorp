<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Friendship;

class CreateTestFriendship extends Command
{
    protected $signature = 'test:create-friendship {user1_id} {user2_id}';
    protected $description = 'Cria uma amizade aceita entre dois usuários para teste';

    public function handle()
    {
        $user1Id = $this->argument('user1_id');
        $user2Id = $this->argument('user2_id');

        $user1 = User::find($user1Id);
        $user2 = User::find($user2Id);

        if (!$user1 || !$user2) {
            $this->error('Usuários não encontrados!');
            return 1;
        }

        if ($user1Id === $user2Id) {
            $this->error('Não é possível criar amizade consigo mesmo!');
            return 1;
        }

        // verificar se ja existe amizade
        $existingFriendship = Friendship::where(function ($q) use ($user1Id, $user2Id) {
            $q->where('user_id', $user1Id)->where('friend_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('user_id', $user2Id)->where('friend_id', $user1Id);
        })->first();

        if ($existingFriendship) {
            $this->info("Amizade já existe com status: {$existingFriendship->status}");

            if ($existingFriendship->status !== 'accepted') {
                $existingFriendship->update(['status' => 'accepted']);
                $this->info('Status atualizado para "accepted"');
            }

            return 0;
        }

        // criar nova amizade
        $friendship = Friendship::create([
            'user_id' => $user1Id,
            'friend_id' => $user2Id,
            'status' => 'accepted'
        ]);

        $this->info("Amizade criada entre {$user1->name} e {$user2->name}");
        $this->info("Friendship ID: {$friendship->id}");
        $this->info("Status: {$friendship->status}");

        return 0;
    }
}
