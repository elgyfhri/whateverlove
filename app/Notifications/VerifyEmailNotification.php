<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $code = (string) random_int(100000, 999999);

        $notifiable->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_expires_at' => now()->addHour(),
        ])->save();

        return (new MailMessage)
            ->subject('Kode Verifikasi Email WHATEVERLOVE')
            ->view('emails.verify', ['code' => $code, 'name' => $notifiable->name]);
    }
}
