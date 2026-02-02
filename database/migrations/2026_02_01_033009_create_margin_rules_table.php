<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('margin_rules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->nullable(); 
            // si luego vuelves multiempresa (por ahora puedes dejar null)

            $table->string('channel'); 
            // web | marketplace

            $table->string('type');    
            // normal | oferta

            $table->decimal('margin_percent', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('margin_rules');
    }
};
