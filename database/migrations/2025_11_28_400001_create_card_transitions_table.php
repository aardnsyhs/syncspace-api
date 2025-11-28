<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('card_transitions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('card_id')->constrained()->cascadeOnDelete();
      $table->foreignId('from_column_id')->nullable()->constrained('columns')->nullOnDelete();
      $table->foreignId('to_column_id')->constrained('columns')->cascadeOnDelete();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->timestamp('transitioned_at');

      $table->index(['card_id', 'transitioned_at']);
      $table->index(['to_column_id', 'transitioned_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('card_transitions');
  }
};
