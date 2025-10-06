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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('invoice_date');
            $table->date('due_date'); // data de vencimento
            $table->foreignId('supplier_id')
                ->constrained('entities')
                ->onDelete('restrict'); // fornecedor
            $table->foreignId('supplier_order_id')->nullable()
                ->constrained()->onDelete('set null'); // encomenda fornecedor
            $table->decimal('total_amount', 10, 2);
            $table->string('document_path')->nullable(); //anexow
            $table->string('payment_proof_path')->nullable(); // comprovativo de pag
            $table->enum('status', ['pending_payment', 'paid'])
                ->default('pending_payment');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
