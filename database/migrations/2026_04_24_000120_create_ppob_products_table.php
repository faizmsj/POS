<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppob_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ppob_providers')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('margin_percent', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppob_products');
    }
};
