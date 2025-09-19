<?php

require_once 'vendor/autoload.php';

use App\Events\DirectMessageSent;
use App\Models\DirectMessage;
use App\Models\DirectConversation;
use App\Models\User;

// bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testando broadcast de DirectMessages...\n\n";

// buscar uma conversa existente
$conversation = DirectConversation::with('users')->first();

if (!$conversation) {
    echo "Nenhuma conversa encontrada. Crie uma conversa primeiro.\n";
    exit(1);
}

echo "Conversa encontrada: ID {$conversation->id}\n";
echo " Usuários: " . $conversation->users->pluck('name')->join(', ') . "\n\n";

// buscar o primeiro usuário da conversa
$user = $conversation->users->first();

// criar uma mensagem de teste
$message = $conversation->messages()->create([
    'sender_id' => $user->id,
    'body' => 'Teste de broadcast - ' . now()->format('H:i:s'),
]);

// Carregar o relacionamento sender
$message->load('sender');

echo "📨 Mensagem criada:\n";
echo "   ID: {$message->id}\n";
echo "   De: {$message->sender->name}\n";
echo "   Conteúdo: {$message->body}\n";
echo "   Conversa: {$message->direct_conversation_id}\n\n";

echo "📡 Enviando broadcast...\n";

try {
    // Disparar o evento de broadcast
    broadcast(new DirectMessageSent($message))->toOthers();

    echo " Broadcast enviado com sucesso!\n";
    echo "Canal: direct-conversation.{$conversation->id}\n";
    echo "Evento: DirectMessageSent\n\n";

    echo "🔍 Verifique o console do navegador para ver se a mensagem foi recebida.\n";

} catch (Exception $e) {
    echo " Erro ao enviar broadcast: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nTeste concluído!\n";
