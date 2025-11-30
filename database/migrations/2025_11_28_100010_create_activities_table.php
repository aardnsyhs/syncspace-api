<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('activities', function (Blueprint $table) {
      $table->id();
      $table->foreignId('card_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->string('type'); 
      $table->json('data')->nullable(); 
      $table->timestamp('created_at');

      $table->index(['card_id', 'created_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('activities');
  }
};
