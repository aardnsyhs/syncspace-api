<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('labels', function (Blueprint $table) {
      $table->id();
      $table->foreignId('team_id')->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->string('color', 7); 
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('labels');
  }
};
