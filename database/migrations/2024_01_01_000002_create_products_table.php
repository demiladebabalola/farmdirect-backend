<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // matches frontend's string "id" e.g. "garri", "tomatoes"
            $table->string('name');
            $table->string('category');
            $table->unsignedInteger('price'); // in Naira, whole numbers as used in mock data
            $table->string('unit'); // e.g. "kg", "bundle", "500g"
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->string('location');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // array of image urls
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('stock'); // free text as in mock e.g. "24 modules available"
            $table->text('description')->nullable();
            $table->string('badge')->nullable(); // e.g. "Best Seller", "Top Rated", "Just In"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
