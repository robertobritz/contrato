<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objeto_contratos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contratante_id')->constrained('contratantes')->cascadeOnDelete();
            $table->foreignUuid('contratado_id')->constrained('contratados')->cascadeOnDelete();
            $table->string('tipo'); // servico ou produto
            $table->text('descricao');
            $table->decimal('quantidade', 10, 2)->default(1);
            $table->string('unidade')->nullable();
            $table->decimal('valor', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objeto_contratos');
    }
};
