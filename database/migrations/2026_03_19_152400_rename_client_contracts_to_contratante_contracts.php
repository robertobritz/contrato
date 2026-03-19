<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('client_contracts', 'contratante_contracts');

        Schema::table('contratante_contracts', function (Blueprint $table) {
            $table->renameColumn('client_id', 'contratante_id');
        });
    }

    public function down(): void
    {
        Schema::table('contratante_contracts', function (Blueprint $table) {
            $table->renameColumn('contratante_id', 'client_id');
        });

        Schema::rename('contratante_contracts', 'client_contracts');
    }
};
