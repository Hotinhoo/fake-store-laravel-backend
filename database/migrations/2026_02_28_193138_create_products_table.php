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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique(); // ID da API Externa

            // Informações do produto
            $table->string('title');
            $table->decimal('price', 10, 2); // Preço com decimal
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('image')->nullable();

            // Rating
            $table->decimal('rating_rate', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);

            // Log de alterações
            $table->json('update_log')->nullable();

            // Soft Deletes
            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index('category');
            $table->index('price');
            $table->index('rating_rate');
            
            // Fulltext index para busca por título parcial
            $table->fullText('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
