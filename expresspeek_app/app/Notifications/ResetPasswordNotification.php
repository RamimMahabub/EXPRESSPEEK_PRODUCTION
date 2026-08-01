<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your Express Peek password')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('We received a request to reset the password for your Express Peek account.')
            ->action('Reset password securely', $url)
            ->line('This secure link expires in '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes and can only be used once.')
            ->line('If you did not request this change, you can safely ignore this email. Your password will remain unchanged.')
            ->salutation('Express Peek Security');
    }
}
