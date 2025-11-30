<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    
    DB::statement("ALTER TABLE team_user MODIFY COLUMN role ENUM('owner', 'admin', 'member', 'viewer') DEFAULT 'member'");
  }

  public function down(): void
  {
    
    DB::table('team_user')->where('role', 'viewer')->update(['role' => 'member']);
    DB::statement("ALTER TABLE team_user MODIFY COLUMN role ENUM('owner', 'admin', 'member') DEFAULT 'member'");
  }
};
