<?php
namespace App\Security\Voter;

use App\Entity\Figure;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class UserVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {

        return  $subject instanceof Figure;
    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Figure $figure */
        $figure = $subject;

        /** @var User|null $user */
        $user = $token->getUser();
       
        // Si l'utilisateur n'est pas connecté, il n'a pas accès
        if (!$user) {
            return false;
        }
        if ($user !== $figure->getAuthor()){
            return false;
        }
        // Vérifie si l'utilisateur est l'auteur de la figure
        return true;
    }
}