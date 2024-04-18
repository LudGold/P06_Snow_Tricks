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
            ->from('nepasrepondre@test.fr')
            ->to($user->getEmail())
            ->subject('Confirmation Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'confirmationUrl' => $confirmationUrl,
            ]);
    
        $this->mailer->send($email);
    }
    
}
