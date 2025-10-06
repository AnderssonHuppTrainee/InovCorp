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
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('order_date');
            $table->foreignId('supplier_id')
                ->constrained('entities')
                ->onDelete('restrict'); // apenas fornecedores
            $table->foreignId('order_id')->constrained()
                ->onDelete('cascade'); // order primaria
            $table->decimal('total_amount', 10, 2)
                ->default(0);
            $table->enum('status', ['draft', 'closed'])
                ->default('draft');
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
        Schema::dropIfExists('supplier_orders');
    }
};
