<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('attachments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('card_id')->constrained()->cascadeOnDelete();
      $table->string('file_name');
      $table->string('file_path'); 
      $table->unsignedBigInteger('file_size')->nullable(); 
      $table->string('mime_type')->nullable();
      $table->boolean('is_external')->default(false); 
      $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
      $table->timestamps();

      $table->index('card_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('attachments');
  }
};
