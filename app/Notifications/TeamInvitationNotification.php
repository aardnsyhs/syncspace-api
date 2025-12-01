<?php

namespace App\Notifications;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification
{
  use Queueable;

  public Team $team;
  public User $inviter;
  public string $role;

  public function __construct(Team $team, User $inviter, string $role)
  {
    $this->team = $team;
    $this->inviter = $inviter;
    $this->role = $role;
  }

  public function via($notifiable): array
  {
    return ['mail'];
  }

  public function toMail($notifiable): MailMessage
  {
    $appName = config('app.name');
    $frontendUrl = config('app.frontend_url');
    $loginUrl = $frontendUrl . '/login';

    return (new MailMessage)
      ->subject('You\'ve been invited to join ' . $this->team->name . ' - ' . $appName)
      ->greeting('Hi ' . $notifiable->name . ',')
      ->line($this->inviter->name . ' has invited you to join **' . $this->team->name . '** workspace on ' . $appName . '.')
      ->line('You\'ve been assigned the role of **' . ucfirst($this->role) . '**.')
      ->line('')
      ->line('Click the button below to sign in and start collaborating:')
      ->action('Join Workspace', $loginUrl)
      ->line('If you don\'t have an account yet, you can create one when you click the link above.')
      ->line('Looking forward to having you on the team!')
      ->salutation('Best regards,')
      ->salutation('The ' . $appName . ' Team');
  }

  public function toArray($notifiable): array
  {
    return [
      //
    ];
  }
}
