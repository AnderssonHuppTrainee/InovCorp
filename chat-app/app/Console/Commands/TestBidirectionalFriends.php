<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Friendship;

class TestBidirectionalFriends extends Command
{
    protected $signature = 'test:bidirectional-friends {user1_id} {user2_id}';
    protected $description = 'Testa se a relação de amigos é bidirecional entre dois usuários';

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

        $this->info("Testando relação bidirecional entre {$user1->name} e {$user2->name}");
        $this->newLine();


        $friendship = Friendship::where(function ($q) use ($user1Id, $user2Id) {
            $q->where('user_id', $user1Id)->where('friend_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('user_id', $user2Id)->where('friend_id', $user1Id);
        })->first();

        if (!$friendship) {
            $this->error('Nenhuma amizade encontrada entre estes usuários!');
            return 1;
        }

        $this->info("Status da amizade: {$friendship->status}");
        $this->newLine();


        $user1Friends = $user1->friends()->get();
        $user1HasUser2 = $user1Friends->contains('id', $user2Id);

        $this->info("Amigos de {$user1->name}:");
        foreach ($user1Friends as $friend) {
            $this->line("- {$friend->name} (ID: {$friend->id})");
        }
        $this->info("{$user1->name} tem {$user2->name} como amigo: " . ($user1HasUser2 ? 'SIM' : 'NÃO'));
        $this->newLine();


        $user2Friends = $user2->friends()->get();
        $user2HasUser1 = $user2Friends->contains('id', $user1Id);

        $this->info("Amigos de {$user2->name}:");
        foreach ($user2Friends as $friend) {
            $this->line("- {$friend->name} (ID: {$friend->id})");
        }
        $this->info("{$user2->name} tem {$user1->name} como amigo: " . ($user2HasUser1 ? 'SIM' : 'NÃO'));
        $this->newLine();


        if ($user1HasUser2 && $user2HasUser1) {
            $this->info('✅ SUCESSO: Relação bidirecional funcionando!');
        } else {
            $this->error('ERRO: Relação não é bidirecional!');
            $this->error("User1 tem User2: " . ($user1HasUser2 ? 'SIM' : 'NÃO'));
            $this->error("User2 tem User1: " . ($user2HasUser1 ? 'SIM' : 'NÃO'));
        }

        return 0;
    }
}
