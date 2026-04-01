<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');           
            $table->text('first_name');   
            $table->text('last_name');    
            $table->text('birth_date');   
            $table->text('phone')->nullable(); 
            $table->softDeletes();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};