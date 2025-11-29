<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('notifications', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->string('type'); // card_assigned, comment, mention, due_soon, card_moved
      $table->string('title');
      $table->text('message');
      $table->json('data')->nullable(); // Additional data like card_id, board_id, etc.
      $table->timestamp('read_at')->nullable();
      $table->timestamps();

      $table->index(['user_id', 'read_at']);
      $table->index(['user_id', 'created_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('notifications');
  }
};
