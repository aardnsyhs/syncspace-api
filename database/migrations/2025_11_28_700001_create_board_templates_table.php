<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('board_templates', function (Blueprint $table) {
      $table->id();
      $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->text('description')->nullable();
      $table->string('slug')->unique();
      $table->enum('visibility', ['global', 'team'])->default('team');
      $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
      $table->timestamps();

      $table->index(['visibility', 'team_id']);
    });

    Schema::create('board_template_columns', function (Blueprint $table) {
      $table->id();
      $table->foreignId('board_template_id')->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->unsignedInteger('position')->default(0);
      $table->unsignedInteger('wip_limit')->nullable();
      $table->timestamps();

      $table->index(['board_template_id', 'position']);
    });

    Schema::create('board_template_cards', function (Blueprint $table) {
      $table->id();
      $table->foreignId('board_template_column_id')->constrained()->cascadeOnDelete();
      $table->string('title');
      $table->text('description')->nullable();
      $table->unsignedInteger('position')->default(0);
      $table->timestamps();

      $table->index(['board_template_column_id', 'position']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('board_template_cards');
    Schema::dropIfExists('board_template_columns');
    Schema::dropIfExists('board_templates');
  }
};
