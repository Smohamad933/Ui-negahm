<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title')->nullable()->default('');
            $table->text('description')->nullable()->default('');
            $table->string('media_url');
            $table->string('media_type', 16)->default('image');
            $table->integer('order_index')->default(0);
            $table->boolean('featured_home')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
