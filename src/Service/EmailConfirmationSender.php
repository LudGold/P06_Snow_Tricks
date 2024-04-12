<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;

class EmailConfirmationSender
{

    public function __construct(private MailerInterface $mailer, private readonly RequestStack $requestStack)
    {
    }

    public function sendConfirmationEmail(User $user, $confirmationUrl): void
    {
        $host = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();

        $email = (new TemplatedEmail())
            ->from('nepasrepondre@test.fr')
            ->to($user->getEmail())
            ->subject('Confirmation Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'confirmationUrl' => $host . '/' . $confirmationUrl,
            ]);

        $this->mailer->send($email);
    }
}
