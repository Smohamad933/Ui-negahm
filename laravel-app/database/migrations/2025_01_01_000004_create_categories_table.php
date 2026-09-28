<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable()->default('');
            $table->string('cover_image_url')->nullable()->default('');
            $table->string('aspect_ratio', 8)->default('16:9');
            $table->integer('order_index')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['client_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
