<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailConfirmationSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private $entityManager;
    private EmailConfirmationSender $emailSender;
    private $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager, 
        EmailConfirmationSender $emailSender,
        UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->emailSender = $emailSender;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('success', 'Vous êtes déjà connecté');
            return $this->redirectToRoute('app_main_homepage');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path: '/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $entityManager = $this->entityManager;
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();
                // Generate reset password URL
                $resetPasswordUrl = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // Send reset password email
                $this->emailSender->sendResetPasswordEmail($user, $resetPasswordUrl);


                $this->addFlash('success', 'Un e-mail de réinitialisation de mot de passe a été envoyé à votre adresse e-mail.');
            } else {
                $this->addFlash('error', 'Adresse e-mail non trouvée.');
            }

            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, string $token): Response
    {
        $entityManager = $this->entityManager;
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        
        if (!$user) {
            throw $this->createNotFoundException('Token invalide.');
        }
    
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
       
           // Hashage du nouveau mot de passe
           $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);

           // Réinitialisation du token et enregistrement du nouveau mot de passe haché
           $user->setResetToken(null);
           $user->setPassword($hashedPassword);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_login');
        }
    
        // Passer la variable resetToken au modèle Twig
        $resetPasswordUrl = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
    
        return $this->render('security/new_password.html.twig', [
            'resetPasswordUrl' => $resetPasswordUrl,
            'token' => $token,
            'resetToken' => $user->getResetToken(), // Passer resetToken au modèle Twig
        ]);
    }
    
    #[Route(path: '/new-password/{token}', name: 'app_new_password')]
    public function newPassword(Request $request, string $token): Response
    {
        return $this->render('security/new_password.html.twig', [
            'token' => $token,
        ]);
    }
}
