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
        Schema::create('digital_archives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('description')->nullable();
            $table->string('document_type');
            //relação polimorgica
            $table->unsignedBigInteger('archivable_id')->nullable();
            $table->string('archivable_type')->nullable();

            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_public')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['archivable_id', 'archivable_type']);
            $table->index('document_type');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_archives');
    }
};
