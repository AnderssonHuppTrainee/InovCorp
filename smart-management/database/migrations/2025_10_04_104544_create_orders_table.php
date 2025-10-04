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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('order_date');
            $table->foreignId('client_id')->constrained('entities')
                ->onDelete('restrict'); // aenas clientes
            $table->foreignId('proposal_id')->nullable()->constrained()
                ->onDelete('set null'); // pode vir de proposta
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['draft', 'closed'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index('number');
            $table->index(['client_id', 'status']);

        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()
                ->constrained('entities')->onDelete('restrict'); // fornecedor na linha
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();

            $table->index(['order_id', 'article_id']);
        });

        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('order_date');
            $table->foreignId('supplier_id')->constrained('entities')
                ->onDelete('restrict'); // apenas fornecedores
            $table->foreignId('order_id')->constrained()
                ->onDelete('cascade'); // order primaria
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['draft', 'closed'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('orders_items');
        Schema::dropIfExists('supplier_orders');
    }
};
