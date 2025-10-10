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
        // Entities - Campos encriptados precisam ser maiores
        Schema::table('entities', function (Blueprint $table) {
            $table->string('tax_number', 500)->change();
            $table->string('name', 500)->change();
            $table->string('email', 500)->nullable()->change();
            $table->string('phone', 500)->nullable()->change();
            $table->string('mobile', 500)->nullable()->change();
            $table->text('address')->change();
            $table->string('postal_code', 500)->change();
        });

        // Contacts - Campos encriptados
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('phone', 500)->nullable()->change();
            $table->string('mobile', 500)->nullable()->change();
            $table->string('email', 500)->nullable()->change();
            $table->text('observations')->nullable()->change();
        });

        // Companies - Campos encriptados
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name', 500)->change();
            $table->text('address')->nullable()->change();
            $table->string('postal_code', 500)->nullable()->change();
            $table->string('city', 500)->nullable()->change();
            $table->string('tax_number', 500)->nullable()->change();
            $table->string('phone', 500)->nullable()->change();
            $table->string('email', 500)->nullable()->change();
            $table->string('website', 500)->nullable()->change();
        });

        // Bank Accounts - Campos encriptados
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('account_number', 500)->change();
            $table->string('iban', 500)->nullable()->change();
        });

        // Articles - reference encriptado
        Schema::table('articles', function (Blueprint $table) {
            $table->string('reference', 500)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para tamanho padrão (não recomendado se já houver dados encriptados)
        Schema::table('entities', function (Blueprint $table) {
            $table->string('tax_number', 255)->change();
            $table->string('name', 255)->change();
            $table->string('email', 255)->nullable()->change();
            $table->string('phone', 255)->nullable()->change();
            $table->string('mobile', 255)->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('phone', 255)->nullable()->change();
            $table->string('mobile', 255)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('tax_number', 255)->nullable()->change();
            $table->string('phone', 255)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('account_number', 255)->change();
            $table->string('iban', 255)->nullable()->change();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('reference', 255)->change();
        });
    }
};
