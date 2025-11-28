<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Comment;
use App\Models\Team;
use App\Policies\BoardPolicy;
use App\Policies\CardPolicy;
use App\Policies\ColumnPolicy;
use App\Policies\CommentPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Register policies
    Gate::policy(Team::class, TeamPolicy::class);
    Gate::policy(Board::class, BoardPolicy::class);
    Gate::policy(Column::class, ColumnPolicy::class);
    Gate::policy(Card::class, CardPolicy::class);
    Gate::policy(Comment::class, CommentPolicy::class);
  }
}
