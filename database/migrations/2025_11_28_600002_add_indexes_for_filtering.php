<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    // Add indexes for better filter performance
    Schema::table('cards', function (Blueprint $table) {
      $table->index('assignee_id');
      $table->index('due_date');
      $table->fullText(['title', 'description'], 'cards_search_fulltext');
    });
  }

  public function down(): void
  {
    Schema::table('cards', function (Blueprint $table) {
      $table->dropIndex(['assignee_id']);
      $table->dropIndex(['due_date']);
      $table->dropFullText('cards_search_fulltext');
    });
  }
};
