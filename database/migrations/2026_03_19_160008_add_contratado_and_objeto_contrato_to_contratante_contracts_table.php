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
            $table->foreignUuid('contratado_id')->nullable()->after('contratante_id')->constrained('contratados')->nullOnDelete();
            $table->foreignUuid('objeto_contrato_id')->nullable()->after('contratado_id')->constrained('objeto_contratos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratante_contracts', function (Blueprint $table) {
            $table->dropForeign(['objeto_contrato_id']);
            $table->dropForeign(['contratado_id']);
            $table->dropColumn(['objeto_contrato_id', 'contratado_id']);
        });
    }
};
