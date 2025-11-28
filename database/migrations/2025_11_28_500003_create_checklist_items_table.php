<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('checklist_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('checklist_id')->constrained()->cascadeOnDelete();
      $table->string('title');
      $table->boolean('is_completed')->default(false);
      $table->unsignedInteger('position')->default(0);
      $table->timestamp('completed_at')->nullable();
      $table->timestamps();

      $table->index(['checklist_id', 'position']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('checklist_items');
  }
};
