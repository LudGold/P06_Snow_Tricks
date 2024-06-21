<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Service\EmailConfirmationSender;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
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
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {

            $avatar = new Avatar();
            $avatar->setName('avatar');
            $avatar->setImageUrl('defaultavatar.jpg');
            $avatar->setPath('defaultavatar.jpg');
            $user->setAvatar($avatar);

             
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()  // Attention à bien utiliser le nom du champ 'plainPassword'
                )
            );
            
            // Générer le token de confirmation d'email
            $user->generateEmailConfirmationToken();
            $entityManager->persist($user);
            $entityManager->flush();
    
            $confirmationUrl = $this->generateUrl('confirm_email', ['emailConfirmationToken' => $user->getEmailConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
    
            $emailConfirmationSender->sendConfirmationEmail($user, $confirmationUrl);
    
            $this->addFlash('success', 'Un email a été envoyé afin de valider votre inscription.');
            return $this->redirectToRoute('homepage');
        }
    
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route("confirm-email/{emailConfirmationToken}", name: 'confirm_email')]

    public function confirmEmail(

        string $emailConfirmationToken,
        EntityManagerInterface $entityManager
    ): Response {

        // Récupérez l'utilisateur associé au token de confirmation
        $user = $entityManager->getRepository(User::class)->findOneBy(['emailConfirmationToken' => $emailConfirmationToken]);

        // Vérifier si l'utilisateur existe avec ce token
        if (!$user) {
            throw $this->createNotFoundException('Email confirmation token is invalid.');
        }

        // Marquer l'utilisateur comme ayant confirmé son email
        $user->setIsVerified(true);
        $user->setEmailConfirmationToken(null);
        $user->setRoles(['ROLE_USER']);
        // Enregistrer les modifications dans la base de données
        $entityManager->flush();
        $this->addFlash('success', 'votre compte est bien validé, vous pouvez vous connecter dès à présent.');
        // Rediriger vers une page après la confirmation de l'email

        return $this->redirectToRoute('app_login');
 
    }
}
    
