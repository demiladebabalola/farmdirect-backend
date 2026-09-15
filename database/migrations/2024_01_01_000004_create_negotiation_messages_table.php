<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negotiation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id')->constrained()->cascadeOnDelete();
            $table->enum('side', ['buyer', 'farmer']); // matches frontend's message.side
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiation_messages');
    }
};
