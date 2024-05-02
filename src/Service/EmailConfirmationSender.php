<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class EmailConfirmationSender
{

    public function __construct(private MailerInterface $mailer, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function sendConfirmationEmail(User $user, string $confirmationUrl): void
    {

        $email = (new TemplatedEmail())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Confirmation Email')
            ->htmlTemplate('email/mail_confirmation_register.html.twig')
            ->context([
                'confirmationUrl' => $confirmationUrl,
            ]);

        $this->mailer->send($email);
    }
    public function sendResetPasswordEmail(User $user, string $resetPasswordUrl): void
    {
        $email = (new TemplatedEmail())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Mot de passe oublié')
            ->htmlTemplate('email/mail_new_password.html.twig')
            ->context([
                'resetPasswordUrl' => $resetPasswordUrl,
                'token'  => $user->getResetToken(),
            ]);

        $this->mailer->send($email);
    }
}
