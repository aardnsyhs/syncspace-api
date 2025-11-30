<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('activities', function (Blueprint $table) {
      
      $table->foreignId('board_id')->after('id')->constrained()->cascadeOnDelete();

      $table->foreignId('card_id')->nullable()->change();

      $table->index(['board_id', 'created_at']);
    });
  }

  public function down(): void
  {
    Schema::table('activities', function (Blueprint $table) {
      $table->dropForeign(['board_id']);
      $table->dropColumn('board_id');
      $table->dropIndex(['board_id', 'created_at']);
    });
  }
};
