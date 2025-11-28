<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('boards', function (Blueprint $table) {
      $table->boolean('is_public')->default(false)->after('color');
      $table->string('public_token', 36)->nullable()->unique()->after('is_public');
      $table->timestamp('public_expires_at')->nullable()->after('public_token');
    });
  }

  public function down(): void
  {
    Schema::table('boards', function (Blueprint $table) {
      $table->dropColumn(['is_public', 'public_token', 'public_expires_at']);
    });
  }
};
