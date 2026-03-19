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
        Schema::table('contratante_contracts', function (Blueprint $table) {
            $table->unique(['contract_id', 'contratante_id', 'objeto_contrato_id'], 'contratante_contracts_unique');
            $table->dropIndex('client_contracts_contract_id_client_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratante_contracts', function (Blueprint $table) {
            $table->unique(['contract_id', 'contratante_id']);
            $table->dropIndex('contratante_contracts_unique');
        });
    }
};
