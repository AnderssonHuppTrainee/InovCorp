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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('client_id')
                ->constrained('entities')
                ->onDelete('restrict');
            $table->foreignId('assigned_to')
                ->constrained('users')
                ->onDelete('restrict');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                ->default('medium');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum(
                'status',
                ['pending', 'in_progress', 'completed', 'cancelled']
            )->default('pending');
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
