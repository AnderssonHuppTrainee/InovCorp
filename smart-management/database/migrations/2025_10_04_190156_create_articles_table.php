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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Referência
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Preço
            $table->foreignId('tax_rate_id')->constrained()->onDelete('restrict'); // IVA
            $table->string('photo')->nullable(); // Foto
            $table->text('observations')->nullable(); // Observações
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('reference');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
