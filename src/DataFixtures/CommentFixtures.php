<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\DataFixtures\FigureFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CommentFixtures extends Fixture implements DependentFixtureInterface 


{
    public const COMMENT_REFERENCE = 'comment-ref';

    public function load(ObjectManager $manager): void
    {$figures = [];
        $i = 0;
        while ($this->hasReference(FigureFixtures::FIGURE_REFERENCE . '_' . $i)) {
            $figures[] = $this->getReference(FigureFixtures::FIGURE_REFERENCE . '_' . $i);
            $i++;
        }
        $users = [];
        for ($i = 0; $this->hasReference(UserFixtures::USER_REFERENCE . '_' . $i); $i++) {
            $users[] = $this->getReference(UserFixtures::USER_REFERENCE . '_' . $i);
        }
        $commentsData = json_decode(file_get_contents(__DIR__ . '/commentsDatas.json'), true);

        $allComments = [];
        $allUsers = [];
        foreach ($commentsData as $commentAttr) {
            $comment = new Comment();
            $comment->setContent($commentAttr['content'])
                      ->setCreatedAt(new \DateTimeImmutable());

            // Attribuer aléatoirement une figure au commentaire
            $randomFigure = $figures[array_rand($figures)];
            $comment->setFigure($randomFigure);
            $randomUser = $users[array_rand($users)];
            $comment->setUser($randomUser);
            $manager->persist($comment);
            $allComments[] = $comment;
            $allUsers[]= $users;
        }

        $manager->flush();

        // Référence unique pour les commentaires
        $this->addReference(self::COMMENT_REFERENCE, $comment);
    }
    public function getDependencies()
    {
        return [
            FigureFixtures::class,
            UserFixtures::class,
        ];
    }
   
}
