<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_grants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('record_id')
                  ->constrained('medical_records')
                  ->onDelete('cascade');
            $table->foreignUlid('doctor_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignUlid('granted_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['record_id', 'doctor_id']);
            $table->timestamps();
            $table->index(['doctor_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_grants');
    }
};