<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface

{
    public const COMMENT_REFERENCE = 'comment-ref';

    public function load(ObjectManager $manager): void
    {

        $commentsData = json_decode(file_get_contents(__DIR__ . '/commentsDatas.json'), true);

        $usersDatas = json_decode(file_get_contents(__DIR__ . '/usersDatas.json'), true);
        $figuresData = json_decode(file_get_contents(__DIR__ . '/figuresDatas.json'), true);

        $numberOfUsers = count($usersDatas);
        $numberOfFigures = count($figuresData);

        foreach ($commentsData as $commentAttr) {
            $comment = new Comment();
            $comment->setContent($commentAttr['content'])
                ->setCreatedAt(new \DateTimeImmutable());

            // Attribuer aléatoirement un utilisateur au commentaire
            $randomIndexUsers = rand(0, $numberOfUsers - 1);
            $randomUser = $this->getReference('user-ref-' . $randomIndexUsers);
            $comment->setUser($randomUser);

            // Attribuer aléatoirement une figure au commentaire
            $randomIndexFigures = rand(0, $numberOfFigures - 1);
            $randomFigure = $this->getReference('figure-ref-' . $randomIndexFigures);
            $comment->setFigure($randomFigure);

            $manager->persist($comment);
        }

        $manager->flush();

        // Référence unique pour les commentaires
        $this->addReference(self::COMMENT_REFERENCE, $comment);
    }

    public function getDependencies()
    {
        return [
            FigureFixtures::class,
        ];
    }
}
