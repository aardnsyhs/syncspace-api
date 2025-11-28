<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('cards', function (Blueprint $table) {
      $table->id();
      $table->foreignId('column_id')->constrained()->cascadeOnDelete();
      $table->string('title');
      $table->text('description')->nullable();
      $table->unsignedInteger('position')->default(0);
      $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
      $table->date('due_date')->nullable();
      $table->timestamps();

      $table->index(['column_id', 'position']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('cards');
  }
};
