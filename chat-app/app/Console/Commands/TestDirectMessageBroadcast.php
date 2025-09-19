<?php

namespace App\Console\Commands;

use App\Events\DirectMessageSent;
use App\Models\DirectMessage;
use App\Models\DirectConversation;
use App\Models\User;
use Illuminate\Console\Command;

class TestDirectMessageBroadcast extends Command
{
    protected $signature = 'test:dm-broadcast {conversation_id} {message}';
    protected $description = 'Test DirectMessage broadcasting by creating a test message';

    public function handle()
    {
        $conversationId = $this->argument('conversation_id');
        $messageBody = $this->argument('message');


        $conversation = DirectConversation::find($conversationId);
        if (!$conversation) {
            $this->error("Conversa com ID {$conversationId} não encontrada.");
            return 1;
        }


        $user = $conversation->users->first();
        if (!$user) {
            $this->error("Nenhum usuário encontrado na conversa.");
            return 1;
        }


        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $messageBody,
        ]);


        $message->load('sender');

        $this->info("Mensagem criada com ID: {$message->id}");
        $this->info("Enviando broadcast...");


        broadcast(new DirectMessageSent($message))->toOthers();

        $this->info("Broadcast enviado com sucesso!");
        $this->info("Canal: direct-conversation.{$conversationId}");
        $this->info("Evento: DirectMessageSent");

        return 0;
    }
}