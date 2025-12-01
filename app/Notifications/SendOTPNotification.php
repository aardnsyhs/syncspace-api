<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOTPNotification extends Notification
{
  use Queueable;

  public string $otp;
  public int $expiryMinutes;

  public function __construct(string $otp, int $expiryMinutes = 5)
  {
    $this->otp = $otp;
    $this->expiryMinutes = $expiryMinutes;
  }

  public function via($notifiable): array
  {
    return ['mail'];
  }

  public function toMail($notifiable): MailMessage
  {
    $appName = config('app.name');

    return (new MailMessage)
      ->subject('Verify Your Email - ' . $appName)
      ->greeting('Hi ' . $notifiable->name . ',')
      ->line('Thank you for registering with ' . $appName . '!')
      ->line('To complete your registration, please use the following verification code:')
      ->line('')
      ->line('**' . chunk_split($this->otp, 3, ' ') . '**')
      ->line('')
      ->line('This code will expire in ' . $this->expiryMinutes . ' minutes.')
      ->line('If you didn\'t create an account with us, you can safely ignore this email.')
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
