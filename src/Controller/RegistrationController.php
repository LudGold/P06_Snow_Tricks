<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailConfirmationSender;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]

    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EmailConfirmationSender $emailConfirmationSender,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Générer le token de confirmation d'email
            $user->generateEmailConfirmationToken();

            $entityManager->persist($user);
            $entityManager->flush();
            // URL de confirmation
            $confirmationUrl = $this->generateUrl('confirm_email', ['token' => $user->getEmailConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);


            // do anything else you need here, like send an email
            $emailConfirmationSender->sendConfirmationEmail($user, $confirmationUrl);


            return $this->redirectToRoute('app_main_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route("confirm-email/{token}", name: 'confirm_email')]

    public function confirmEmail(Request $request, string $token, EntityManagerInterface $entityManager): Response
    {
        // Récupérez l'utilisateur associé au token de confirmation
        $user = $entityManager->getRepository(User::class)->findOneBy(['emailConfirmationToken' => $token]);
        // Vérifier si l'utilisateur existe avec ce token
        if (!$user) {
            throw $this->createNotFoundException('Email confirmation token is invalid.');
        }

        // Marquer l'utilisateur comme ayant confirmé son email
        $user->setIsVerified(true);
        $user->setEmailConfirmationToken(null);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Rediriger vers une page après la confirmation de l'email
        return $this->redirectToRoute('app_main_homepage');
    }
}
