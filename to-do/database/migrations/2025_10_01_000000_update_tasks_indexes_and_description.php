<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            try {
                $table->index('status', 'tasks_status_index');
            } catch (\Throwable $e) {

            }

            try {
                $table->index('priority', 'tasks_priority_index');
            } catch (\Throwable $e) {

            }

            try {
                $table->index('due_date', 'tasks_due_date_index');
            } catch (\Throwable $e) {

            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            try {
                $table->dropIndex('tasks_status_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('tasks_priority_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('tasks_due_date_index');
            } catch (\Throwable $e) {
            }
        });
    }
};


