<?php
namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_edit_user')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserInterface $user, SluggerInterface $slugger): Response
    {
        if (!$user instanceof User) {
            throw new \LogicException('User is not an instance of User entity.');
        }

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $avatarFile = $userForm->get('avatar')->getData();

            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les erreurs lors du déplacement du fichier
                }

                $avatar = $user->getAvatar() ?? new Avatar();
                $avatar->setImageUrl($newFilename);
                $avatar->setPath($newFilename);
                $user->setAvatar($avatar);

                $entityManager->persist($avatar);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avatar a été mis à jour.');

            return $this->redirectToRoute('app_edit_user');
        }

        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
}
