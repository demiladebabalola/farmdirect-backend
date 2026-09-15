<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('buyer_offer'); // matches frontend's "buyerOffer"
            $table->unsignedInteger('farmer_ask');  // matches frontend's "farmerAsk"
            $table->enum('status', ['Offer Sent', 'Counter-Offer Received', 'Accepted', 'Rejected'])
                  ->default('Offer Sent');
            $table->unsignedInteger('settled_price')->nullable(); // price both parties agreed on
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiations');
    }
};
