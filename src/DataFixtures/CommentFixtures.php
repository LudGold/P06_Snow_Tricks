<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $comment = new Comment();
        $comment
            
            ->setContent('Contenu du commentaire');
            
            

        $manager->persist($comment);

        $manager->flush();
    }
}
