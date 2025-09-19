<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Friendship;

class ListAllFriendships extends Command
{
    protected $signature = 'test:list-friendships';
    protected $description = 'Lista todas as amizades e testa a relação bidirecional';

    public function handle()
    {
        $this->info('=== LISTANDO TODAS AS AMIZADES ===');
        $this->newLine();


        $friendships = Friendship::with(['user', 'friend'])->get();

        $this->info("Total de friendships na tabela: {$friendships->count()}");
        $this->newLine();

        foreach ($friendships as $friendship) {
            $this->line("ID: {$friendship->id} | {$friendship->user->name} -> {$friendship->friend->name} | Status: {$friendship->status}");
        }

        $this->newLine();
        $this->info('=== TESTANDO RELAÇÃO BIDIRECIONAL ===');
        $this->newLine();


        $users = User::all();

        foreach ($users as $user) {
            $friends = $user->friends()->get();
            $this->info("{$user->name} (ID: {$user->id}) tem {$friends->count()} amigos:");

            foreach ($friends as $friend) {
                $this->line("  - {$friend->name} (ID: {$friend->id})");
            }
            $this->newLine();
        }


        $acceptedFriendships = Friendship::where('status', 'accepted')->with(['user', 'friend'])->get();

        $this->info('=== VERIFICANDO BIDIRECIONALIDADE ===');
        $this->newLine();

        foreach ($acceptedFriendships as $friendship) {
            $user1 = $friendship->user;
            $user2 = $friendship->friend;

            $user1Friends = $user1->friends()->get();
            $user2Friends = $user2->friends()->get();

            $user1HasUser2 = $user1Friends->contains('id', $user2->id);
            $user2HasUser1 = $user2Friends->contains('id', $user1->id);

            $status = ($user1HasUser2 && $user2HasUser1) ? '✅' : '❌';

            $this->line("{$status} {$user1->name} <-> {$user2->name}");
            $this->line("   {$user1->name} tem {$user2->name}: " . ($user1HasUser2 ? 'SIM' : 'NÃO'));
            $this->line("   {$user2->name} tem {$user1->name}: " . ($user2HasUser1 ? 'SIM' : 'NÃO'));
            $this->newLine();
        }

        return 0;
    }
}
