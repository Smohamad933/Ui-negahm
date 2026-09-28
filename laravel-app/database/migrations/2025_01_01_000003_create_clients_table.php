<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('logo_url')->nullable()->default('');
            $table->string('cover_image_url')->nullable()->default('');
            $table->string('accent_color')->nullable()->default('');
            $table->text('short_description')->nullable()->default('');
            $table->string('industry')->nullable()->default('');
            $table->string('website_url')->nullable()->default('');
            $table->string('year')->nullable()->default('');
            $table->boolean('featured')->default(false);
            $table->boolean('published')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
