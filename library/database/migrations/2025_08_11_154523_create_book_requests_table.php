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
        Schema::create('book_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            //num unico
            $table->string('number')->unique();

            $table->date('request_date');
            $table->date('expected_return_date');
            $table->date('actual_receipt_date')->nullable();

            $table->integer('actual_days')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'returned', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_requests');
    }
};
