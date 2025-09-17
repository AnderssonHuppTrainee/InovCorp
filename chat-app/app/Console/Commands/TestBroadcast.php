<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:broadcast {room_id} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test broadcasting a message to a room';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roomId = $this->argument('room_id');
        $messageText = $this->argument('message');

        // Criar uma mensagem de teste
        $message = new \App\Models\Message([
            'id' => 999999,
            'body' => $messageText,
            'room_id' => $roomId,
            'sender_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simular o sender
        $message->sender = new \App\Models\User([
            'id' => 1,
            'name' => 'Test User',
        ]);

        $this->info("Enviando mensagem de teste para sala {$roomId}: {$messageText}");

        try {
            broadcast(new \App\Events\MessageSent($message));
            $this->info("✅ Broadcast enviado com sucesso!");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao enviar broadcast: " . $e->getMessage());
        }
    }
}
