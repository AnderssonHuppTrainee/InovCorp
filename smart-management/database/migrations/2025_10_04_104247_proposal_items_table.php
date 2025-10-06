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
        Schema::create('proposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()
                ->onDelete('cascade');
            $table->foreignId('article_id')->constrained()
                ->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()
                ->constrained('entities')
                ->onDelete('restrict'); // fornecedor na linha
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('cost_price', 10, 2)
                ->nullable(); // preço de custo
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['proposal_id', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_items');
    }
};
