<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('cpf');
            $table->string('rg')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('profession')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('address_complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cpf']);
            $table->unique(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratados');
    }
};
