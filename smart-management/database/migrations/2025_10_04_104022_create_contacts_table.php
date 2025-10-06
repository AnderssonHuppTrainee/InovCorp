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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('contact_role_id')->constrained()
                ->onDelete('restrict');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->text('observations')->nullable();
            $table->enum('status', ['active', 'inactive'])
                ->default('active');
            $table->timestamps();

            $table->index(['entity_id', 'status']);
            $table->index('contact_role_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
