<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('checklists', function (Blueprint $table) {
      $table->id();
      $table->foreignId('card_id')->constrained()->cascadeOnDelete();
      $table->string('title');
      $table->unsignedInteger('position')->default(0);
      $table->timestamps();

      $table->index(['card_id', 'position']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('checklists');
  }
};
