<?php

namespace App\DataFixtures;

use App\Entity\Comment;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface

{
    public const COMMENT_REFERENCE = 'comment-figure';

    public function load(ObjectManager $manager): void
    {
        $figures = [];
        for ($i = 0; $this->hasReference(FigureFixtures::FIGURE_REFERENCE . '_' . $i); $i++) {
            $figures[] = $this->getReference(FigureFixtures::FIGURE_REFERENCE . '_' . $i);
        }

        $commentsData = json_decode(file_get_contents(__DIR__ . '/commentsDatas.json'), true);

        $allComments = [];
        foreach ($commentsData as $commentAttr) {
            $comment = new Comment();
            $comment->setContent($commentAttr['content'])
                ->setCreatedAt(new \DateTimeImmutable());

            // Attribuer aléatoirement une figure au commentaire
            $randomFigure = $figures[array_rand($figures)];
            $comment->setFigure($randomFigure);

            $manager->persist($comment);
            $allComments[] = $comment;
        }

        $manager->flush();

        // Référence unique pour les commentaires
        $this->addReference(self::COMMENT_REFERENCE, $allComments);
    }

    public function getDependencies()
    {
        return [
            FigureFixtures::class,
        ];
    }
}
