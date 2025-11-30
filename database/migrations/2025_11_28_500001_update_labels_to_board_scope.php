<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    
    Schema::table('labels', function (Blueprint $table) {
      $table->unsignedBigInteger('board_id')->nullable()->after('id');
    });

    DB::statement('
            UPDATE labels l
            SET l.board_id = (
                SELECT b.id FROM boards b WHERE b.team_id = l.team_id ORDER BY b.id LIMIT 1
            )
            WHERE l.board_id IS NULL
        ');

    DB::table('labels')->whereNull('board_id')->delete();

    Schema::table('labels', function (Blueprint $table) {
      $table->unsignedBigInteger('board_id')->nullable(false)->change();
      $table->foreign('board_id')->references('id')->on('boards')->cascadeOnDelete();
    });

    Schema::table('labels', function (Blueprint $table) {
      $table->dropForeign(['team_id']);
      $table->dropColumn('team_id');
    });
  }

  public function down(): void
  {
    Schema::table('labels', function (Blueprint $table) {
      $table->unsignedBigInteger('team_id')->nullable()->after('id');
    });

    DB::statement('
            UPDATE labels l
            SET l.team_id = (
                SELECT b.team_id FROM boards b WHERE b.id = l.board_id
            )
        ');

    Schema::table('labels', function (Blueprint $table) {
      $table->unsignedBigInteger('team_id')->nullable(false)->change();
      $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
      $table->dropForeign(['board_id']);
      $table->dropColumn('board_id');
    });
  }
};
