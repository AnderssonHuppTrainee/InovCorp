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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->date('event_date');
            $table->time('event_time');
            $table->integer('duration')->default(60);
            $table->json('shared_with')->nullable(); // partilhado (array de user_ids)
            $table->boolean('knowledge')->default(false); // conhecimento
            $table->foreignId('entity_id')->nullable()->constrained()
                ->onDelete('cascade');
            $table->foreignId('calendar_event_type_id')
                ->constrained()->onDelete('restrict'); // tipo
            $table->foreignId('calendar_action_id')
                ->constrained()->onDelete('restrict'); // ação
            $table->text('description');
            $table->foreignId('user_id')->constrained()
                ->onDelete('cascade');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])
                ->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_date', 'user_id']);
            $table->index('entity_id');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
        Schema::dropIfExists('calendar_event_types');
        Schema::dropIfExists('calendar_actions');
    }
};
