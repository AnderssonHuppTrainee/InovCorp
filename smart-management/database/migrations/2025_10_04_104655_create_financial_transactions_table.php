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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()
                ->onDelete('restrict');
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->string('reference')->nullable();
            $table->date('transaction_date');
            $table->json('metadata')->nullable(); // para dados adicionais
            $table->timestamps();

            $table->index(['bank_account_id', 'transaction_date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
