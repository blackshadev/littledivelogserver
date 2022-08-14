<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Users\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class UserEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private User $user, private string $verificationUrl)
    {
    }

    public function build(): self
    {
        return $this
            ->to($this->user->getEmail())
            ->subject('User Email Verification')
            ->view('mail.user.email_verification', [
                'verificationUrl' => $this->verificationUrl,
                'name' => $this->user->getName(),
            ]);
    }
}
