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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('tax_number')->unique();
            $table->json('types');
            $table->string('name');
            $table->text('address');
            $table->string('postal_code');
            $table->string('city');
            $table->foreignId('country_id')->constrained()
                ->onDelete('restrict');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->text('observations')->nullable();
            $table->enum('status', ['active', 'inactive'])
                ->default('active');
            $table->timestamps();
            $table->softDeletes();

            // indexes
            $table->index('tax_number');
            $table->index('status');
            $table->index(['types'], 'entities_types_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
