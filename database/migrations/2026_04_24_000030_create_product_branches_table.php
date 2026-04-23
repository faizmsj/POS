<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->decimal('stock', 15, 3)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('margin_percent', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unique(['product_id', 'branch_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_branches');
    }
};
