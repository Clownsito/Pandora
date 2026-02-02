<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');

            // Stock semáforo
            $table->unsignedInteger('stock_rojo_max');
            $table->unsignedInteger('stock_amarillo_min');
            $table->unsignedInteger('stock_verde_min');

            // Márgenes existentes
            $table->unsignedDecimal('margen_min_percent', 5, 2);
            $table->unsignedDecimal('margen_max_percent', 5, 2);

            // NUEVOS campos para márgenes dinámicos
            $table->unsignedDecimal('margin_web_standard', 5, 2)->default(25.00)->after('stock_verde_min');
            $table->unsignedDecimal('margin_marketplace_diff', 5, 2)->default(12.00)->after('margin_web_standard');

            $table->timestamps();
        });

        // Foreign key separada (MariaDB safe)
        Schema::table('company_settings', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};