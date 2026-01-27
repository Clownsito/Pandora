<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cached_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('sku');
            $table->string('name');
            $table->unsignedDecimal('cost', 12, 2);
            $table->integer('stock');

            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'sku']);
            $table->index(['company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cached_products');
    }
};
