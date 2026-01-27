<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('approved_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cached_product_id')
                ->constrained('cached_products')
                ->cascadeOnDelete();

            $table->foreignId('marketplace_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('sale_price', 12, 2);
            $table->decimal('gross_margin_percent', 5, 2);
            $table->decimal('real_margin_percent', 5, 2);

            $table->enum('status', ['ok', 'rejected']);

            $table->foreignId('approved_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('approved_at');

            $table->timestamps();

            $table->unique([
                'cached_product_id',
                'marketplace_id'
            ], 'approved_unique_product_marketplace');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approved_prices');
    }
};
