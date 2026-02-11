<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user');
            $table->string('title');
            $table->text('text');
            $table->string('asignatura');
            $table->string('approved')->default('pending');
            $table->string('status')->default('active');
            $table->string('timestamp');
            $table->string('dangerous_content')->default('false');
            $table->text('approve_reason')->nullable();
            $table->text('delete_reason')->nullable();
            $table->string('moderated_at')->nullable();
            $table->string('moderated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
