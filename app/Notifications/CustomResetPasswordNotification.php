<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
  use Queueable;

  public string $token;

  public function __construct(string $token)
  {
    $this->token = $token;
  }

  public function via($notifiable): array
  {
    return ['mail'];
  }

  public function toMail($notifiable): MailMessage
  {
    $url = config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);
    $appName = config('app.name');
    $expireMinutes = config('auth.passwords.users.expire');

    return (new MailMessage)
      ->subject('Reset Your Password - ' . $appName)
      ->greeting('Hi ' . $notifiable->name . ',')
      ->line('We received a request to reset your password for your ' . $appName . ' account.')
      ->line('Click the button below to create a new password:')
      ->action('Reset Password', $url)
      ->line('This link will expire in ' . $expireMinutes . ' minutes for security reasons.')
      ->line('If you didn\'t request a password reset, you can safely ignore this email. Your password will remain unchanged.')
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
