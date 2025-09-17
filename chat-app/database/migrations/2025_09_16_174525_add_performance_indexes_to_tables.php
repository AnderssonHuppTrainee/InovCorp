<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para tabela messages
        Schema::table('messages', function (Blueprint $table) {
            // Índice composto para buscar mensagens por sala ordenadas por data
            $table->index(['room_id', 'created_at'], 'idx_messages_room_created');

            // Índice para buscar mensagens por remetente
            $table->index('sender_id', 'idx_messages_sender');

            // Índice para mensagens diretas
            $table->index(['direct_conversation_id', 'created_at'], 'idx_messages_direct_created');
        });

        // Índices para tabela room_user
        Schema::table('room_user', function (Blueprint $table) {
            // Índice para buscar salas de um usuário
            $table->index('user_id', 'idx_room_user_user');

            // Índice para buscar usuários de uma sala
            $table->index('room_id', 'idx_room_user_room');
        });

        // Índices para tabela rooms
        Schema::table('rooms', function (Blueprint $table) {
            // Índice para buscar salas por criador
            $table->index('created_by', 'idx_rooms_creator');

            // Índice para buscar salas públicas
            $table->index(['private', 'created_at'], 'idx_rooms_private_created');
        });

        // Índices para tabela direct_conversation_user
        Schema::table('direct_conversation_user', function (Blueprint $table) {
            // Índice para buscar conversas de um usuário
            $table->index('user_id', 'idx_direct_conv_user');

            // Índice para buscar usuários de uma conversa
            $table->index('direct_conversation_id', 'idx_direct_conv_conv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices da tabela messages
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_room_created');
            $table->dropIndex('idx_messages_sender');
            $table->dropIndex('idx_messages_direct_created');
        });

        // Remover índices da tabela room_user
        Schema::table('room_user', function (Blueprint $table) {
            $table->dropIndex('idx_room_user_user');
            $table->dropIndex('idx_room_user_room');
        });

        // Remover índices da tabela rooms
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_creator');
            $table->dropIndex('idx_rooms_private_created');
        });

        // Remover índices da tabela direct_conversation_user
        Schema::table('direct_conversation_user', function (Blueprint $table) {
            $table->dropIndex('idx_direct_conv_user');
            $table->dropIndex('idx_direct_conv_conv');
        });
    }
};
