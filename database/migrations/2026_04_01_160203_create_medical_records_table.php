<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_id')
                  ->constrained('patient_profiles')
                  ->onDelete('cascade');
            $table->foreignUlid('created_by')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->string('title');
            $table->text('diagnosis');   
            $table->text('treatment');            
            $table->string('files_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('patient_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};